#!/usr/bin/env python3

from multiprocessing import Process

def count(n):
    while n > 0:
        n -= 1

p1 = Process(target=count,args=(10**8,))
p1.start()
p2 = Process(target=count,args=(10**8,))
p2.start()
p1.join(); p2.join()