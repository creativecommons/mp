pandoc --smart "platform-toolkit.mdwn" -w html5 -o "platform-toolkit.html" --toc --template template.html -V "title: Platform Toolkit" -V "lastmodified: $(date)"  -fmarkdown-implicit_figures
