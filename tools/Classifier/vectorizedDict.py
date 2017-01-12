#!/usr/bin/env python
# -*- coding: utf-8 -*-
# vim: tabstop=8 expandtab shiftwidth=4 softtabstop=4

""" Instance classifier """

from sklearn import tree
from sklearn.feature_extraction import DictVectorizer


measurements = [
    {'city': 'Dubai', 'temperature': 33.},
    {'city': 'London', 'temperature': 12.},
    {'city': 'San Fransisco', 'temperature': 28.},
]


def testClassfiy(measurements):

    vec = DictVectorizer()

    cities, temperatures = zip(*measurements)

    print cities
    print temperatures

    X = vec.fit_transform(cities).toarray()
    Y = [0, 1, 0]

    print X
    # print vec.get_feature_names()
    print Y

    clf = tree.DecisionTreeClassifier()
    clf = clf.fit(X, Y)

    prediction = clf.predict(X)

    print prediction


if __name__ == "__main__":
    testClassfiy(measurements)
