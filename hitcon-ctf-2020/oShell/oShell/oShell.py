#!/usr/bin/python
# coding: UTF-8

import os
import sys
import signal
# import socket
import getpass
# import threading
from time import sleep
from distutils.spawn import find_executable
from subprocess import CalledProcessError, call

IS_ENABLED  = False
SECRET_FILE = '/enable.secret'

COMMANDS = [
    'id', 
    'ping', 
    'ping6', 
    'traceroute', 
    'traceroute6', 
    'arp', 
    'netstat', 
    'top', 
    'htop',
]

PRIV_COMMANDS = [
    'ifconfig', 
    'tcpdump', 
]

COLORS = {
    'header': '\033[95m', 
    'blue': '\033[94m', 
    'cyan': '\033[96m', 
    'green': '\033[92m', 
    'warning': '\033[93m', 
    'fail': '\033[91m', 
    'endc': '\033[0m', 
    'bold': '\033[1m', 
    'underline': '\033[4m', 
    'blink': '\033[5m', 
}

def _signal_handler(sig, frame):
    sys.stdout.write('\n' + _color(get_prompt(), 'green'))
    sys.stdout.flush()

def _sanity_check():
    for cmd in COMMANDS:
        assert find_executable(cmd), "'%s' not found!" % cmd

    assert os.path.exists(SECRET_FILE), "'%s' not found!" % SECRET_FILE

def _print_banner():
    print _color('Welcome to ', 'bold')
    print _color('''
        __  _            _  _ 
  ___  / _\\| |__    ___ | || |
 / _ \\ \\ \\ | '_ \\  / _ \\| || |
| (_) |_\\ \\| | | ||  __/| || |
 \\___/ \\__/|_| |_| \\___||_||_|
'''.lstrip('\n'), 'bold')
        
def _cmd(cmd, args):
    cmds = [cmd]
    if args:

        cmds += args
    try:
        return call(cmds)
    except CalledProcessError as e:
        return e.output

def _color(s, color=''):
    code = COLORS.get(color)
    if code:
        return COLORS['bold'] + code + s + COLORS['endc'] + COLORS['endc']
    else:
        return s

def _exit(msg):
    print _color("\n\n" + msg, 'warning')
    os._exit(1)

# def syslog(message, level=7, facility=1): # debug and user
#     host = os.environ.get('LOG_HOST', '127.0.0.1')

#     sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
#     message = '<oshell-logger> %s' % message
#     data = '<%d>%s' % (level + facility*8, message)
#     sock.sendto(data, (host, 514))
#     sock.close()

def get_prompt():
    if IS_ENABLED:
        prompt = '(enabled) oshell~# '
    else:
        prompt = 'oshell~$ '

    return prompt

def usage():
    cmds = []
    cmds.append('help')
    cmds.append('exit')
    cmds.extend(COMMANDS)
    if IS_ENABLED:
        cmds.extend(PRIV_COMMANDS)
    cmds.append('enable')

    print _color('Available commands: ', "bold")
    print '  ' + '\n  '.join(cmds)

signal.signal(signal.SIGINT, _signal_handler)

if __name__ == '__main__':

    _sanity_check()
    _print_banner()

    while True:
        
        prompt = get_prompt()
        line = raw_input(_color(prompt, 'green'))
        line = line.strip()
        if not line:
            continue

        if ' ' in line:
            cmd, args = line.split(' ', 1)
            args = args.split(' ')
        else:
            cmd = line
            args = None
        
        # dispatch 
        if cmd == 'exit' or cmd == 'quit' or cmd == 'q':
            _exit('Bye!')

        if cmd == 'help' or cmd == '?':
            usage()

        elif cmd in COMMANDS:
            _cmd(cmd, args)

        elif IS_ENABLED and cmd in PRIV_COMMANDS:
            _cmd(cmd, args)

        elif cmd == 'enable':
            user_secret = getpass.getpass()

            with open(SECRET_FILE, 'rb') as fp:
                real_secret = fp.read().strip()

            if user_secret == real_secret:
                IS_ENABLED = True
            else:
                sleep(1)
                print _color('Wrong password :(')
            
        else:
            print 'command not found, try "help"'
