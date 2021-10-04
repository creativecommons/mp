# mp

> The [Platform Toolkit][mp-prod] is a material to guide content sharing
> platforms when adding the option of CC licensing.  These aren't hard-set
> requirements, but suggestions on how to make the implementation smooth for
> users and platform alike. The process towards building it was described in a
> series of [blog posts][revamp] on the CC Open Source blog.

[mp-prod]: https://creativecommons.org/platform/toolkit/
[revamp]: https://opensource.creativecommons.org/blog/entries/cc-platform-toolkit-revamp/ "CC Platform Toolkit Revamp — Creative Commons on GitHub"


## Getting started

The toolkit is placed in the `docs/` folder along with a frozen copy of the
[Vocabulary][vocabulary] stylesheets. The site is predominately self-contained.
You should be able to see nearly accurate rendering by cloning this repository
and opening `docs/index.html` in your browser.

[vocabulary]: https://github.com/creativecommons/vocabulary


## Hosting and Deployment

Within both the Creative Commons production and staging environments, the
`docs/` directory is served by the NGINX web server. The Creative Commons
production and staging environments are managed using SaltStack (see the
[`nginx.misc`][nginx-misc] state for specifics). SaltStack ensures the
repository clone is at the most recent version for the configured branch when
the state is applied.

[nginx-misc]: https://github.com/creativecommons/sre-salt-prime/blob/main/states/nginx/misc.sls


## Historical Files

Everything pertaining to the previous toolkit and model platform material is
placed in the `deprecated/` folder. This folder will eventually be deleted
(though it will remain in the git history).


## License


### Code / Scripts

[`LICENSE`](LICENSE) (Expat/[MIT][mit] License)

[mit]: http://www.opensource.org/licenses/MIT "The MIT License | Open Source Initiative"


### Content

![CC0 1.0 Universal license button][cc0-png]

To the extent possible under law, Creative Commons has waived all copyright and
related or neighboring rights to this work ([CC0 1.0 Universal][cc0]).

[cc0-png]: https://licensebuttons.net/l/zero/1.0/88x31.png "CC0 1.0 Universal license button"
[cc0]: https://creativecommons.org/publicdomain/zero/1.0/ "Creative Commons — CC0 1.0 Universal"
