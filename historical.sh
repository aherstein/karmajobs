#!/bin/bash

subreddits=$(cat jobslist.txt)
for subreddit in $subreddits
do
    curl -XPUT --user aherstein@gmail.com:nintendo  http://dev.karmajobs.net/api/historical/$subreddit
    echo -e "\n"
done