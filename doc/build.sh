pandoc --smart "platform-toolkit.mdwn" -w html5 -o "platform-toolkit.html" --toc --template template.html -fmarkdown-implicit_figures -V "title: Platform Toolkit" -V "lastmodified: $(date)"
