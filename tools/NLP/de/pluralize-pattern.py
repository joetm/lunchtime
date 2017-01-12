#!/usr/bin/env python
# -*- coding: utf-8 -*-
# vim: tabstop=8 expandtab shiftwidth=4 softtabstop=4

"""
    Return the plural form of a given German word

    Warning: 84% accuracy for singularization and 72% for pluralization.
    Warning: Works only well if input is in singular already.
    Warning: does not work with German Umlauts
        - E.g. ß -> UnicodeDecodeError: 'ascii' codec can't decode byte 0xc3 in position 13: ordinal not in range(128)
"""

import sys

from pattern.de import singularize, pluralize


def findPlural(word):
    """
    :param word: The word to convert into plural.
    :raise BadValueError: If `recurse and not save`.
    :return: The pluralized word.
    """

    word = word.encode('ascii', 'ignore')

    # first singularize the word
    # word = singularize(word)
    # print "singular: %s" % word
    # then pluralize it
    word = pluralize(word)
    # print "plural: %s" % word
    return word


if __name__ == "__main__":

    if len(sys.argv) != 2:
        print "Missing argument. Usage example: %s Hund" % sys.argv[0]
        # raise ValueError('Missing argument. Usage example: %s Hund' % sys.argv[0])
        sys.exit(1)

    try:
        plural = findPlural(sys.argv[1])
    except UnicodeDecodeError:
        print "Input contains non-ascii chars"
        sys.exit(1)

    print plural
