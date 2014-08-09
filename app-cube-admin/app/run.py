#! /bin/env python

import sys
import os
import signal
import fcntl
import time
import subprocess

if len(sys.argv) < 2:
    print >> sys.stderr, "Usage: %s <program>" % sys.argv[0]
    sys.exit(1)

prog_name = os.path.basename(sys.argv[1])
prog_dir = os.path.dirname(sys.argv[1])
prog_argv = sys.argv[1:]
log_dir = os.path.dirname(os.path.realpath(__file__))

lockfilename = "%s/run-%s.lock" % (log_dir, prog_name)
lockfd = os.open(lockfilename, os.O_WRONLY | os.O_CREAT, 0666)
fcntl.flock(lockfd, fcntl.LOCK_EX | fcntl.LOCK_NB)

logfilename = "%s/run-%s.log" % (log_dir, prog_name)
logfp = open(logfilename, "a")

def msg(msg):
    print >> logfp, msg
    logfp.flush()

parent_pid = os.getpid()
parent_pgid = os.getpgid(parent_pid)
daemon_pid = os.fork()
current_pid = os.getpid()
msg('Fork in: %s  parent: %s, daemon: %s' % (current_pid, parent_pid, daemon_pid))
if daemon_pid != 0:
    msg('Parent process exit: %s' % parent_pid)
    sys.exit(0)

child_pid = 0

def sig_handler(sig, frame):
    msg('Signal(%d) caught, kill process group: %s' % (sig, parent_pgid))
    os.killpg(parent_pgid, signal.SIGTERM)
    sys.exit(0)

signal.signal(signal.SIGTERM, sig_handler)
signal.signal(signal.SIGINT, sig_handler)
signal.signal(signal.SIGPIPE, sig_handler)

while True:
    msg("Pid: %s, try running" % current_pid + " ".join(prog_argv))
    sp = subprocess.Popen(prog_argv, cwd=prog_dir, close_fds=True, stdout=logfp, stderr=subprocess.STDOUT)
    child_pid = sp.pid
    msg('Child pid %s' % current_pid)
    sp.wait()
    time.sleep(3)

