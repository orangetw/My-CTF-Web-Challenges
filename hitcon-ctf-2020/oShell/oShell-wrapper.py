#!/usr/bin/python -u
import os, sys
import pty
import uuid
import requests
from time import sleep
from tempfile import mkstemp
from subprocess import check_output

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

def check_token(token):
    def _is_valid_uuid(s):
        try:
            return uuid.UUID(s) is not None
        except:
            return False

    if _is_valid_uuid(token):
        r = requests.get('https://ctf2020.hitcon.org/team/token_auth?token=%s' % token)
        return r.json().get('id')
    # else:
    #     if token == 'orange':
    #         return True

def my_exec(cmds):
    return check_output(cmds)

def _color(s, color=''):
    code = COLORS.get(color)
    if code:
        return COLORS['bold'] + code + s + COLORS['endc'] + COLORS['endc']
    else:
        return s

if __name__ == '__main__':
    token = raw_input(_color('Team token: ', 'bold')).strip()
    if not token or not check_token(token):
        print(_color('Bad token. Bye!\n', 'warning'))
        exit(-1)

    name = 'team-%s' % token
    cmds = [
        'sudo', 
        'docker', 'ps', '-q', 
        '-f', 'name=%s' % name
    ]
    container_id = my_exec(cmds)
    if container_id:
        print(_color('[*] Connecting to initialized instance...\n', 'bold'))
    else:
        print(_color('[*] Initializing instance...\n', 'bold'))

        _, tmp_name = mkstemp(prefix='%s_'%name, dir='/home/orange/tmp/')
        with open(tmp_name, 'wb+') as fp:
            fp.write('this-is-secret-' + os.urandom(8).encode('hex'))

        os.chmod(tmp_name, 0o444)
        cmds = [
            'sudo', 
            'docker', 'rm', '-f', name
        ]
        try:
            with open(os.devnull, 'w') as devnull:
                check_output(cmds, stderr=devnull)
        except:
            pass

        cmds = [
            'sudo', 
            'docker', 'run', '-d', '--rm', 
            '--env', 'LOG_HOST=172.17.0.1', 
            '-v', '%s:/enable.secret' % tmp_name, 
            '--name', name, 
            'oshell'
        ]
        my_exec(cmds)
        sleep(2)

    cmds = [
        'sudo', 
        'docker', 'exec', '-ti', 
        '-u', 'oShell', 
        name, 
        'python', '/oShell.py', 'tty'
    ]

    pty.spawn(cmds)