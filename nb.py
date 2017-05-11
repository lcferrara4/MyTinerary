"""Naive Bayes Classifier to classify sites, prints results as id[list of values]"""

use_bigrams = False # turn on bigram features for 3a
delta = 0.01        # smoothing
verbose = False      # turn on answers for 1abc

import sys
import collections
import math

### Read in the data

def read_data(fn):
    for line in open(fn):
        [id, k, cat, rating, food, bar] = line.rstrip().split('|')[0:6]
        [sight, shop, art, museum, theater, sports] = line.rstrip().split('|')[6:12]
        yield id, str(k + ' ' + cat), rating, food, bar, sight, shop, art, museum, theater, sports

train = read_data("train.txt")
dev = read_data(sys.argv[1])
stopWords = set([line.rstrip() for line in open('stopWords.txt', 'r').readlines()])

### Features

def gen_features(words):
    global stopWords
    feats = [] #['<bias>']  # always-on feature
    clean_words = [x.lower() for x in words.split() if x not in stopWords]
    feats.extend(clean_words) # unigram features
    if use_bigrams:     # bigram features
        for i in range(len(words)-1):
            feats.append('_'.join(words[i:i+2]))
    return feats

### Collect counts

c_k = collections.Counter()
f_kw = collections.defaultdict(collections.Counter)
b_kw = collections.defaultdict(collections.Counter)
s_kw = collections.defaultdict(collections.Counter)
sh_kw = collections.defaultdict(collections.Counter)
a_kw = collections.defaultdict(collections.Counter)
m_kw = collections.defaultdict(collections.Counter)
t_kw = collections.defaultdict(collections.Counter)
sp_kw = collections.defaultdict(collections.Counter)

# Get counts from training data

vocab = set()
for id, cat, r, f, b ,s, sh, a, m, t, sp in train:
    for w in gen_features(cat):
        if w in f_kw:
            f_kw[w] += int(f)
            b_kw[w] += int(b)
            s_kw[w] += int(s)
            sh_kw[w] += int(sh)
            a_kw[w] += int(a)
            m_kw[w] += int(m)
            t_kw[w] += int(t)
            sp_kw[w] += int(sp)
        else:
            f_kw[w] = int(f)
            b_kw[w] = int(b)
            s_kw[w] = int(s)
            sh_kw[w] = int(sh)
            a_kw[w] = int(a)
            m_kw[w] = int(m)
            t_kw[w] = int(t)
            sp_kw[w] = int(sp)
        vocab.add(w)
    f_kw['<unk>'] = 0
    b_kw['<unk>'] = 0
    s_kw['<unk>'] = 0
    sh_kw['<unk>'] = 0
    a_kw['<unk>'] = 0
    m_kw['<unk>'] = 0
    t_kw['<unk>'] = 0
    sp_kw['<unk>'] = 0
vocab.add('<unk>')

### Normalize to get probabilities

z_k = sum(c_k.values())
p_k = {k:c_k[k]/z_k for k in c_k}
pf_kw = {}
pb_kw = {}
ps_kw = {}
psh_kw = {}
pa_kw = {}
pm_kw = {}
pt_kw = {}
psp_kw = {}
all_dicts = [pf_kw, pb_kw, ps_kw, psh_kw, pa_kw, pm_kw, pt_kw, psp_kw,
             f_kw, b_kw, s_kw, sh_kw, a_kw, m_kw, t_kw, sp_kw]


for i in range(0, 8):
    for word in vocab:
        z = sum(all_dicts[i+8].values())
        all_dicts[i][word] = (all_dicts[i+8][word]+delta)/(z+delta*len(vocab))


### Report stats on new data

PASS_VALUE = 0.05
results = ''
for line in list(dev):
    id = line[0]
    desc = line[1]
    guesses = [0, 0, 0, 0, 0, 0, 0, 0]
    correct = line[3:11]
    desc_words = gen_features(desc)

    results += id + '|'
    for word in desc_words:
        if word in pf_kw:
            if pf_kw[word]:
                guesses[0] += pf_kw[word]
                guesses[1] += pb_kw[word]
                guesses[2] += ps_kw[word]
                guesses[3] += psh_kw[word]
                guesses[4] += pa_kw[word]
                guesses[5] += pm_kw[word]
                guesses[6] += pt_kw[word]
                guesses[7] += psp_kw[word]
        else:
                guesses[0] += pf_kw['<unk>']
                guesses[1] += pb_kw['<unk>']
                guesses[2] += ps_kw['<unk>']
                guesses[3] += psh_kw['<unk>']
                guesses[4] += pa_kw['<unk>']
                guesses[5] += pm_kw['<unk>']
                guesses[6] += pt_kw['<unk>']
                guesses[7] += psp_kw['<unk>']

    answer = []
    for guess in guesses:
        #print(guess)
        if guess > PASS_VALUE:
            results += '1|'
            answer.append(1)
        else:
            results += '0|'
            answer.append(0)
    results += '\n'

print results,
