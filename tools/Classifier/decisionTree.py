#!/usr/bin/env python
# -*- coding: utf-8 -*-
# vim: tabstop=8 expandtab shiftwidth=4 softtabstop=4

""" Instance classifier """

from sklearn import tree

# X = [[0, 0], [1, 1]]
# Y = [0, 1]
# clf = tree.DecisionTreeClassifier()
# clf = clf.fit(X, Y)
# print clf.predict([[2., 2.]])


def testClassfiy():

    X = [[0, 0], [1, 1], [0.2, 0]]
    Y = [0, 1, 0]

    clf = tree.DecisionTreeClassifier()
    clf = clf.fit(X, Y)

    print clf.predict([[0.3, 0]])


if __name__ == "__main__":
    testClassfiy()
