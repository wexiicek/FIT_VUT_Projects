#!/usr/bin/env python3

import fileinput

words = set()
wordsr = set()
for line in fileinput.FileInput():
	w = line.rstrip()
	rev = w[::-1]
	if w != rev:
		words.add(w)
		wordsr.add(rev)

print(sorted(list(words & wordsr)))