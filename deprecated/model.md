# Model Platform

As we don't really intend for this to be used by anyone, we can skip a
few pieces of common functionality: user registration, password resets, etc.

We also can assume a few things like using sqlite for a database, which wouldn't happen in a production system usually, etc etc.

## What we want

* Some kind of generic homepage that allows you to login or register (same thing in our model)
* A form for logging in
* A profile showing you all your items (images, text documents)
* A way to browse for items by CC license
* A way to license a work
* A way to batch license some works

