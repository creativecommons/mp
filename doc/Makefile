# See - http://answers.squarespace.com/questions/16924/how-do-i-create-hideshow-text-on-a-page-like-you-see-with-faqs
head = "<!DOCTYPE html>\n<head lang=\"en\">\n<title>Model Platform</title>\n</head>\n<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\" integrity=\"sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7\" crossorigin=\"anonymous\">\n<body>\n<div class=\"container\">"
tail = \
"</div>\n\
<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js\"></script>\n\
<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js\" integrity=\"sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS\" crossorigin=\"anonymous\"></script>\n\
<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n\
<script>\
    \$$(document).ready(function(){\
    \$$('h2').css('cursor','pointer');\
    \$$('h2').nextUntil('h2').slideToggle();\
    \$$('h2').click(function() {\$$(this).nextUntil('h2').slideToggle();});\
    });\
    </script>\n</body>\n</html>"

platform-toolkit.html: platform-toolkit.mdwn
	echo $(head) > platform-toolkit.html
	markdown platform-toolkit.mdwn >> platform-toolkit.html
	echo $(collapsescript) >> platform-toolkit.html
	echo $(tail) >> platform-toolkit.html
clean:
	rm -f platform-toolkit.html
