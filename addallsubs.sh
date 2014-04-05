#!/bin/bash

subreddits=$(cat jobslist.txt)
for subreddit in $subreddits
do
    curl -XPUT --user aherstein@gmail.com:nintendo http://local.karmajobs.net/api/subreddit/$subreddit
    echo -e "\n"
done