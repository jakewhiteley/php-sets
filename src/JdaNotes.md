# Notes on Additions to PhpSets by John Allsup

My webhost at present is a basic package with Ionos. 
There is PHP, MySQL and Apache, but you get what you get, and can't, for exmaple, install things via PECL.
So sets need to be implemented in PHP using arrays behind the scenes, and this is what this PhpSets does.
My modifications are generally the result of 'scratching an itch' -- if it doesn't do something I want, and
can easily be modified to do so, I hack in some code to do it. Thus I share things in a fork.

-- John

## 28th March, 2023

### New Set From Array
The upstream version only allowed instatiating sets by passing elements as arguments, rather than all elements in a single array.
That is, if you have
```php
$elements = [1,2,3];
```
there was no way without using reflection to create a set with those elements. Now you can say
```php
$set = Set::FromArray($elements);
```
though note that, behind the scenes, this just uses reflection. At some point I'll take a deeper look into how it works
and remove the dependency on reflection.

### Family Union and Intersection
Given a set A of sets { a_1, a_2, ..., a_n } the union UA is the collection of things that are members of *at least one* of the a_i.
Similary the intersection of A is the collection of things that are in *every* one of the a_i. The vacuous case of union is the empty set.
But in Set Theory as it appears in modern mathematics, the intersection if the empty set is the 'set of all sets'. (The reason
is that 'X is in every member of A' is true for every X in the case where A is empty, since there is no possibility of a counterexample.
For some X *not* to be in the intersection of A, there needs to be *some* B in A such that X is not in B.)

What is there at present (2023-03-28) is correct, with the exception that the intersection of
an empty family of sets results in the empty set, which is not how things work in actual Set Theory -- see
for example [this math.stackexchange question](https://math.stackexchange.com/questions/959201/the-intersection-of-an-empty-family-of-sets).
It is not efficient as is. It simply iteratively calls the `Set::intersect($set)` method.
