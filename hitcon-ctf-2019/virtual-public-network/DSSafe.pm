package DSSafe;

use strict;
use vars qw($VERSION);
$VERSION = "0.01";

use Fcntl;
use Text::ParseWords;
use Symbol;
use File::Basename;

use Exporter;
use vars qw(@ISA @EXPORT %EXPORT_TAGS);
@ISA = qw(Exporter);
@EXPORT = qw(open popen ppopen close system psystem exec backtick pbacktick
             maketemp untaint is_tainted);
%EXPORT_TAGS = (ds => [qw(ds_open ds_popen ds_ppopen ds_cleanup)]);

use vars qw(@__temp_files %__file_handles %__ppwrite_handles $__debug_level);

INIT {
    @__temp_files = ();
    %__file_handles = ();
    %__ppwrite_handles = ();
    $__debug_level = ();
    __setenv();
};

END {
    ds_cleanup();
}

sub __log {
    my $msg = shift;
    my ($pkg, $file, $line) = caller;
    $file = basename($file);
}

sub __exit {
    my $status = shift;
    exit $status;
}

sub __die {
    my $msg = shift;
    my ($pkg, $file, $line) = caller;
    $file = basename($file);
    __exit(1);
}

sub __setenv {
    $ENV{PATH} = join(":",
                      "/bin",
                      "/usr/bin",
                      "/usr/X11R6/bin",
                      "$ENV{DSINSTALL}/bin",
                      "$ENV{DSINSTALL}/perl5/bin");
    $ENV{PATH} = untaint($ENV{PATH});   
}

# Parse a command. Interpret shell redirects. The command passed in is
# considered as a single command w/o pipes and semicolons. 
sub __parsecmd {
    my $cmd = shift;
    my @args = quotewords('\s+', 1, $cmd);

    my @env = (); # currently not used. pending review.
    my @xargs = (); # arguments of the command
    my ($xcmd, $fout, $fin, $ferr, $mout, $min, $merr, $rd2);

    while (@args) {
        my $arg = shift @args;
        next if (length($arg) == 0);
        unless (defined $xcmd) {
            if ($arg =~ /^(\w+)=(.+)$/) {
                push @env, {$1 => $2};
                next;
            } elsif ($arg =~ /^[^\/a-zA-Z]/) {
                __log("Invalid command: $cmd"); # must be / or letter
                return undef;
            }
            $xcmd = untaint($arg);
            next;
        }
        if ($arg =~ /^(2|1)>&(2|1)$/) {
            $rd2 = $2;
        } elsif ($arg =~ /^(1|2)?(>>?)([^>].*)?$/) {
            if ($1 and $1 == 2) {
                ($merr, $ferr) = ($2, $3 || untaint(shift @args));
            } else {
                ($mout, $fout) = ($2, $3 || untaint(shift @args));
            }
        } elsif ($arg =~ /^(<)(.+)?$/) {
            ($min, $fin) = ($1, $2 || untaint(shift @args));
        } elsif ($arg =~ /^(>&)(.+)?$/) {
            $fout = $ferr = $2 || untaint(shift @args);
            $mout = $merr = ">";
        } elsif ($arg =~ /^(\'|\")(.*)(\'|\")$/) {
            push @xargs, $2; # skip checking meta between quotes
#               } elsif ($arg =~ /[\$\&\*\(\)\{\}\[\]\`\;\|\?\n~<>]/) {
        } elsif ($arg =~ /[\&\*\(\)\{\}\[\]\`\;\|\?\n~<>]/) {
            __log("Meta characters not allowed: ($arg) $cmd");
            return undef;
        } elsif ($arg =~ /\W\$/) {
            __log("Meta characters not allowed: ($arg) $cmd");
        } else {
            push @xargs, untaint($arg);
        }
    }
    if ($rd2) {
        # redirect both 2 and 1 to the same place
        if (defined $fout) {
            ($ferr, $merr) = ($fout, $mout);
        } elsif (defined $ferr) {
            ($fout, $mout) = ($ferr, $merr);
        } elsif ($rd2 == 1) {
            open STDERR, ">&STDOUT" or die "cannot dup STDERR to STDOUT:$!\n";
            select STDERR; $|=1;
            select STDOUT; $|=1;
        } elsif ($rd2 == 2) {
            open STDOUT, ">&STDERR" or die "cannot dup STDOUT to STDERR:$!\n";
            select STDOUT; $|=1;
            select STDERR; $|=1;
        }
    }
    unless ($xcmd) {
        __log("Command parsing error: $cmd");
        return undef;
    }

    # need to untaint $cmd. otherwise the whole hash will be tainted.
    # but $cmd will never be used for exec anyway, only for debug.
    my $params = { cmd => untaint($cmd), xcmd => $xcmd, xargs => \@xargs };
    $params->{fstdout} = $fout if $fout;
    $params->{mstdout} = $mout if $mout;                                
    $params->{fstderr} = $ferr if $ferr;
    $params->{mstderr} = $merr if $merr;
    $params->{fstdin} = $fin if $fin;
    $params->{mstdin} = $min if $min;
    
    return $params;
}

# executed by a child process or our version of exec
sub __execo {
    my $params = shift;

    unless ($params->{mstdout} or $params->{mstderr} or $params->{mstdin}) {
        # if there is no special redirect requirements, we simply execute
        goto execute;
    }
    
    # if stdout goes into a file, we reopen STDOUT to that file
    if (exists $params->{fstdout}) {
        if ($params->{fstdout} eq "/dev/null") {
            CORE::close(STDOUT);
            unless (CORE::open(STDOUT, ">/dev/null")) {
                __die("__execo: cannot open /dev/null");
            }
        } else {
            my $stdout = gensym;
            unless (CORE::open($stdout,
                               $params->{mstdout} . $params->{fstdout})) {
                __die("__execo: cannot open " . $params->{fstdout});
            }
            my $fd = fileno($stdout);
            unless (CORE::open(*STDOUT, ">&=$fd")) {
                __die("__execo: dup STDOUT to " . $params->{fstdout});
            }
            CORE::close($stdout);
        }
    }
    # if stderr goes into a file, we reopen STDERR to that file
    if (exists $params->{fstderr}) {
        if ($params->{fstderr} eq "/dev/null") {
            CORE::close(STDERR);
            unless (CORE::open(STDERR, ">/dev/null")) {
                __die("__execo: cannot open /dev/null");
            }
        } else {
            my $stderr = gensym;
            unless (CORE::open($stderr,
                               $params->{mstderr} . $params->{fstderr})) {
                __die("__execo: cannot open " . $params->{fstderr});
            }
            my $fd = fileno($stderr);
            unless (CORE::open(*STDERR, ">&=$fd")) {
                __die("__execo: dup STDERR to " . $params->{fstderr});
            }
            CORE::close($stderr);
        }
    }
    # if stdin comes from a file. we need to fork a subprocess to open
    # the file and pump it into us
    if (exists $params->{mstdin}) {
        my $pid = CORE::open(*STDIN, "-|");
        unless (defined $pid) {
            __die("__execo: cannot fork for STDIN input");
        }
        if ($pid) {
        } else {
            local *FILE;
            unless (CORE::open(*FILE, $params->{mstdin} . $params->{fstdin})) {
                __die("__execo: cannot open " . $params->{fstdin});
            }
            print <FILE>;
            __exit(0);
        }
    }

    execute:
    __setenv();
    if ($__debug_level > 0) {
        __log("__execo $$: $params->{xcmd}, " .
              join('|', @{$params->{xargs}}));
        __log("__execo $$: redirects: " .
              join(', ', map { "$_: " . ($params->{$_} ? $params->{$_} : "") }
                   (qw(fstdout fstderr fstdin mstdout mstderr mstdin))));
    }
    (@{$params->{xargs}} > 0
     ? CORE::exec $params->{xcmd}, @{$params->{xargs}}
     : CORE::exec $params->{xcmd});

    __die("__execo $$ failed $!");
}

# open regular file only (mostly)
sub open ($$) {
    my $fh = shift;
    my $filename = shift;

    __log("open $fh, $filename");
    
    unless ($fh =~ /^\*(.+::)?(\w+)$/) {
        __log("first parameter must be a file handle name");
        return undef;
    }
    if ($2 eq "STDOUT" or $2 eq "STDERR" or $2 eq "STDIN") {
        eval "\$fh = \*$2";
        eval "CORE::close($2)";
    }
    $filename = ($filename =~ /\s*(.*\S)\s*/)[0];

    my $ffh;
    my $first = substr($filename, 0, 1);
    my $last = substr($filename, -1, 1);
    
    if ($first eq '>' || $first eq '<') {
        # STDOUT, dup e.g. >&=2, <&=0, or dup e.g. >&STDOUT
        if ($filename eq ">-" || $filename =~ /^(<|>)&=\d+$/) {
            return CORE::open($fh, $filename);
        }
        if ($filename =~ /^>&(\w+)$/) {
            unless (($1 eq "STDOUT") or ($1 eq "STDERR") or ($1 eq "STDIN")) {
                my $caller = caller;
                $filename = ">&" . $caller . "::$1";
            }
            return CORE::open($fh, $filename);
        }
        if ($filename =~ /^(>>?)\s*(.+)$/) { # e.g. >>file, >file
            $ffh = ds_open($2, ($1 eq ">" ? "w" : "a"));
        } elsif ($filename =~ /^(<)\s*(.+)$/) { # e.g. <file
            $ffh = ds_open($2, "r");
        }
    } elsif ($first eq '+') {
        if ($filename =~ /^\+<\s*(.+)$/) { # e.g. +< abc
            $ffh = ds_open($1, "r+");
        } elsif ($filename =~ /^\+(>>?)\s*(.+)$/) { # e.g. +> abc, +>>abc
            $ffh = ds_open($2, ($1 eq ">>" ? "a+" : "w+"));
        }
    } elsif ($first eq '|' or $last eq '|') {
        if ($filename eq '|-' or $filename eq '-|') {
            return CORE::open($fh, $filename);
        }
        __log("Open with pipe is not allowed.");
        return undef;
    } elsif ($filename eq '-') {
        return open($fh, $filename); # STDIN
    } else {
        $ffh = ds_open($filename, "r"); # simple file
    }
    unless ($ffh) {
        __log("Open filename '$filename' failed");
        return undef;
    }
    push @{$__file_handles{fileno($ffh)}}, $fh; 
    eval " $fh = *{\$ffh}"; # don't ask me why :)
    return $ffh;
}

sub popen {
    my $fh = shift;
    unless ($fh =~ /^\*/) {
        __log("first parameter must be a file handle name");
        return undef;
    }
    my $ffh = ds_popen(@_);
    return undef unless ($ffh);
    eval " $fh = *{\$ffh}"; 
    return $ffh;
}

sub ppopen {
    my $fh = shift;
    unless ($fh =~ /^\*/) {
        __log("first parameter must be a file handle name");
        return undef;
    }
    my $ffh = ds_ppopen(@_);
    return undef unless ($ffh);
    eval "$fh = *{\$ffh}"; 
    return eval $fh . "{IO}";
}

sub backtick {
    local $SIG{INT} = 'IGNORE';
    local $SIG{QUIT} = 'IGNORE';
    
    my $pipe = ds_popen(shift, "r");
    return undef unless ($pipe);
    my @result = <$pipe>;
    CORE::close($pipe);
    return (wantarray ? @result : join("", @result));
}

sub pbacktick {
    local $SIG{INT} = 'IGNORE';
    local $SIG{QUIT} = 'IGNORE';
    
    my $pipe = ds_ppopen(@_, "r");
    my @result = <$pipe>;
    CORE::close($pipe);
    return (wantarray ? @result : join("", @result));
}
# system3 is kind of multi-arg "system" with redirects
# the last two parameter of system3 call are interpreted, the rest is passed to
# multi-arg exec call which doesn't involve shell
sub system3 {
    my @xargs = @_;
    my $cmdline = join(' ', @xargs);
    my @cmd = (shift @xargs, pop @xargs, pop @xargs);
    my $params = __parsecmd(join(' ', @cmd));
 
    return -1 unless ($params);
 
    $params->{xargs} = \@xargs;
    $params->{cmd} = $cmdline;
 
    # We want SIGINT and SIGQUIT to be ignored in the parent
    # while the child is running.  However, we want the child
    # to get these signals -- so we declare a block around
    # the code that ignores SIGINT such that the child will
    # exec with the signals turned on.
    {
        local $SIG{INT} = 'IGNORE';
        local $SIG{QUIT} = 'IGNORE';
        flush STDOUT; flush STDERR; flush STDIN;
 
        my $pid = fork;
        unless (defined $pid) {
            __log("system: cannot fork $!");
            return -1;
        }
        if ($pid) {
            waitpid $pid, 0;
            return $?;
       }
    }
    return __execo $params;
}

# system2 is kind of multi-arg "system" with redirects
# the last parameter of system2 call is interpreted, the rest is passed to
# multi-arg exec call which doesn't involve shell
sub system2 {
    my @xargs = @_;
    my $cmdline = join(' ', @xargs);
    my @cmd = (shift @xargs, pop @xargs);
    my $params = __parsecmd(join(' ', @cmd));
 
    return -1 unless ($params);
 
    $params->{xargs} = \@xargs;
    $params->{cmd} = $cmdline;
 
    # We want SIGINT and SIGQUIT to be ignored in the parent
    # while the child is running.  However, we want the child
    # to get these signals -- so we declare a block around
    # the code that ignores SIGINT such that the child will
    # exec with the signals turned on.
    {
        local $SIG{INT} = 'IGNORE';
        local $SIG{QUIT} = 'IGNORE';
        flush STDOUT; flush STDERR; flush STDIN;
 
        my $pid = fork;
        unless (defined $pid) {
            __log("system: cannot fork $!");
            return -1;
        }
        if ($pid) {
            waitpid $pid, 0;
            return $?;
       }
    }
    return __execo $params;
}
 
# If last parameter involves redirection, use system2 call instead.
# redirections don't work with CORE::system.
sub system {
    return CORE::system(@_) if (@_ > 1);
    my $params = __parsecmd(join(' ', @_));
    return -1 unless ($params);

    # We want SIGINT and SIGQUIT to be ignored in the parent
    # while the child is running.  However, we want the child
    # to get these signals -- so we declare a block around
    # the code that ignores SIGINT such that the child will
    # exec with the signals turned on.
    {
        local $SIG{INT} = 'IGNORE';
        local $SIG{QUIT} = 'IGNORE';
        flush STDOUT; flush STDERR; flush STDIN;
      
        my $pid = fork;
        unless (defined $pid) {
            __log("system: cannot fork $!");
            return -1;
        }
        if ($pid) {
            waitpid $pid, 0;
            return $?;
        }
    }
    return __execo $params;
}

sub psystem {
    local $SIG{INT} = 'IGNORE';
    local $SIG{QUIT} = 'IGNORE';
    my $pid = fork;
    unless (defined $pid) {
        __log("system: cannot fork $!");
        return -1;
    }
    if ($pid) {
        waitpid $pid, 0;
        return $?;
    }
    my $r = ds_ppopen(@_, "n");
    return ((defined $r) ? $r : -1);
}

# exec2 is kind of multi-arg "exec" with redirects
# the last parameter of exec2 call is interpreted, the rest is passed to
# multi-arg exec call which doesn't involve shell
sub exec2 {
    my @xargs = @_;
    my $cmdline = join(' ', @xargs);
    my @cmd = (shift @xargs, pop @xargs);
    my $params = __parsecmd(join(' ', @cmd));
 
    return -1 unless ($params);
 
    $params->{xargs} = \@xargs;
    $params->{cmd} = $cmdline;
 
    return __execo $params;
}


sub exec {
    return CORE::exec(@_) if (@_ > 1);
    my $params = __parsecmd(join(' ', @_));
    unless ($params) {
        __log("Command parse error @{[ join(' ', @_) ]}");
        return 0;
    }
    return __execo $params;
}

# Only opens regular file. No pipes or commands. Basically fopen() in C.
sub ds_open ($;$) {
    my $file = shift;
    my $mode = shift || "r";
    
    if ($file =~ /\|$/ or $file =~ /^\|/) {
        __log("ds_open() only for regular file. Use popen() for pipe");
        return undef;
    }
    if ($file =~ /[<>]+/) {
        __log("Invalid file name $file");
        return undef;
    }

    if    ($mode eq "r" ) { $mode = O_RDONLY }
    elsif ($mode eq "r+") { $mode = O_RDWR }
    elsif ($mode eq "w" ) { $mode = O_WRONLY | O_TRUNC  | O_CREAT }
    elsif ($mode eq "w+") { $mode = O_RDWR   | O_TRUNC  | O_CREAT }
    elsif ($mode eq "a" ) { $mode = O_APPEND | O_WRONLY | O_CREAT }
    elsif ($mode eq "a+") { $mode = O_APPEND | O_RDWR   | O_CREAT }
    else  {
        __log("Invalid mode $mode to open");
        return undef;
    }
    # Use sysopen to avoid the meta char stuff. Also umask is automatically
    # set to 0666 on the file to write.
    my $fh = gensym;
    unless (sysopen($fh, $file, $mode)) {
        __log("Cannot open $file: $!");
        return undef;
    }
    $__file_handles{fileno($fh)} = [ $fh ];
    return $fh;
}

# Open single-step pipe
sub ds_popen {
    my $params = __parsecmd(shift);
    return undef unless ($params);
    
    my $type = shift || "r";
    my $pipe = ($type eq "r" ? "-|" : "|-");
    
    flush STDOUT; flush STDERR; flush STDIN;

    local *PIPE;
    my $pid = CORE::open(PIPE, $pipe);
    unless (defined $pid) {
        __log("Cannot open pipe");
        return undef;
    }
    if ($pid) {
        return *PIPE;
    }
    return __execo $params;
}

# Open multi-step pipes
sub ds_ppopen {
    my $mode = pop @_;
    unless (grep /$mode/, (qw(r w n))) {
        __log "Invalid mode $mode";
        return undef;
    }
    my @commands;
    for my $i (0..$#_) {
        my $params = __parsecmd($_[$i]);
        return undef unless ($params);
        push @commands, $params;
    }
    local *PIPE;        
    flush STDOUT; flush STDERR; flush STDIN;
    
    my $pipe = ($mode eq "w" ? "|-" : "-|");
    if($mode ne 'n'){
        my $pid = CORE::open(PIPE, $pipe);
        unless (defined $pid) {
            __log("Cannot fork");
            return undef;
        }
        
        if ($pid) {
            $__ppwrite_handles{fileno(*PIPE)} = 1 if ($mode eq "w");
            return *PIPE;
        }
    }

    dopipe:
    # We fork the process to exec commands in the pipe from left to right
    # if it is a "write" pipe, e.g. "|a|b|c", and from right to left 
    # otherwise, e.g. "a|b|c|", or "a|b|c". Therefore if it is not a "write"
    # pipe, the parent (e.g. "c") should dup STDIN to the pipe handle.
    my $params = ($mode eq "w" ? shift @commands : pop @commands);

    my $pipe_fh = gensym;
    if (@commands) {
        my $pid = CORE::open($pipe_fh, $pipe);
        unless (defined $pid) {
            __die("Cannot fork for $params->{xcmd}");
        }
        if ($pid) {
            my $duped;
            my $fd = fileno $pipe_fh;
            if ($mode eq "w") {
                unless (defined $params->{fstdout}) {
                    unless ($duped = CORE::open(STDOUT, ">&=$fd")) {
                        __die("Cannot dup PIPE");
                    }
                }
                unless (defined $params->{fstderr}) {
                    unless ($duped = CORE::open(STDERR, ">&=$fd")) {
                        __die("Cannot dup PIPE");
                    }
                }
            } else {
                unless ($duped = CORE::open(STDIN, "<&=$fd")) {
                    __die("Cannot dup STDIN");
                }
            }
            CORE::close($pipe_fh) if $duped;
            return __execo $params;
        } else {
            goto dopipe; # child, fetch next command to exec.
        }
    } else {
        return __execo $params;
    }
}

sub ds_cleanup {
    __log("ds_cleanup...");
    for my $f (@__temp_files) {
        close($f->[0]);
        if (-f $f->[1]) {
            unlink $f->[1] or __log("Cannot unlink temp file $f->[1]: $!");
        }
    }
    for my $f (keys %__file_handles) {
        for my $fh (@{$__file_handles{$f}}) {
            unless ($fh =~ /(STDOUT)|(STDERR)|(STDIN)/) {
                eval "close(\$fh); undef $fh";
            }
        }
    }
}

sub maketemp {
    my $dir = shift || "/tmp";
    my $tries = 0;
    my $path;
    my $fh = gensym;
    while ($tries++ < 100) {
        $path = $dir . '/ds_' . rand(time() ^ ($$ + ($$ << 15)));
        $path =~ s/\.//g;
        next if (-e $path);
        if (CORE::open($fh, ">$path")) {
            my @ret = ($fh, $path);
            push @__temp_files, \@ret;
            return (wantarray ? @ret : $fh);
        }
    }
    __log("Cannot create temp files in $dir");
    return undef;
}

sub close ($) {
    my $fh = shift;
    my $fileno = fileno $fh;
    CORE::close($fh);
    if (defined $__ppwrite_handles{$fileno}) {
        delete $__ppwrite_handles{$fileno};
        select(undef, undef, undef, 0.001);
    }
    for my $h (@{$__file_handles{$fileno}}) {
        CORE::close($h);
        eval "undef $h";
    }
    delete $__file_handles{$fileno};
}

sub fcntl {
    __log "fcntl is disabled";
    return undef;
}

sub ioctl {
    __log "ioctl is disabled";
    return undef;
}

sub untaint {
    my $arg = shift;
    my $reg = shift if @_;
    
    unless ($reg) {
        if ($arg =~ /^([^\000-\010\013\014\016-\037\177]*)$/) {
            return $1;
        }
        __log("String with unprintable chars detected: $arg");
        return "";
    }
    return "";
}

sub is_tainted {
    local (@_, $@, $^W) = @_;
    not eval { kill 0, join("", @_); 1; }
}

1;

