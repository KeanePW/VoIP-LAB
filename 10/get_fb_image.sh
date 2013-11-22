#!/bin/bash
# This scripts uses cURL and Facebook Graph API to retrieve the user profile picture.
# If the user doesn't exists the script returns with an error code
# Otherwise the image URL will be printed on stdout

# More info about this API: https://developers.facebook.com/docs/reference/api/using-pictures/

USER=$1
URI="http://graph.facebook.com/${USER}/picture?type=large"
CURL=$(which curl)
CURL_OPTS="-s -i"
SED=$(which sed)

[ -z "$USER" ] && exit -1

# If the user exists Facebook answers with a redirect the to the picture.
# I'm using sed to gather the final destination from the Location HTTP header
image_url=$($CURL $CURL_OPTS $URI | $SED -e '/^Location:\s/!d' -e 's/^Location:\s\(.*\)/\1/g')
[ -z "$image_url" ] && exit -1
echo -n  "$image_url"
exit 1
