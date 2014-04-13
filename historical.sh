#!/bin/bash

server="107.170.50.188"
subreddits=$(cat jobslist.txt)
for subreddit in $subreddits
do
    curl -XPUT --user aherstein@gmail.com:nintendo  http://$server/api/historical/$subreddit
    echo -e "\n"
done