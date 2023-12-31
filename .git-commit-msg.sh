#!/usr/bin/env bash

if [[ $1 == ".git/MERGE_MSG" ]]; then
    exit 0
fi

LOCAL_BRANCH=`git branch | grep \* | cut -d ' ' -f2`

if [[ ($LOCAL_BRANCH =~ "release") || ($LOCAL_BRANCH == "master") || ($LOCAL_BRANCH == "main") ]]; then
    exit 0
fi

echo -e -n "\033[1;33mCheck message format: \033[0m";

egrep -q -e '^[a-zA-Z0-9\]+.*?[[:space:]].+$' $1; ERROR=$?;
if [ $ERROR -ne 0 ]; then
    echo -e "\033[0;31mNOT MATCH\033[0m";
    exit $ERROR;
else
    echo -e "\033[0;32mMATCH\033[0m";
fi

echo -e -n "\033[1;33mCheck branch vs message: \033[0m";

COMMIT_BRANCH=`egrep -o -e '^[a-zA-Z0-9\]+' $1`

egrep -q -e "^$COMMIT_BRANCH" <<< "$LOCAL_BRANCH"; ERROR=$?;
if [ $ERROR -ne 0 ]; then
    echo -e "\033[0;31mNOT MATCH\033[0m";
    exit $ERROR;
else
    echo -e "\033[0;32mMATCH\033[0m";
fi
