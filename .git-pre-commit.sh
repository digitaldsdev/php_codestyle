#!/usr/bin/env bash

echo -e "pre-commit actions"

LOCAL_BRANCH=`git branch | grep \* | cut -d ' ' -f2`
echo -e "committing \033[0;32m$LOCAL_BRANCH\033[0m"

CHANGED_FILES=`git status -s | cut -c4- | grep "\.php"`
if [[ $CHANGED_FILES ]]; then
    echo -e "find files: ${CHANGED_FILES}"

    set -e

    export DEFAULT_USER="1000";
    export DEFAULT_GROUP="1000";

    export USER_ID=`id -u`
    export GROUP_ID=`id -g`
    export USER=$USER

    test -e docker/.env || { cp docker/.env.example docker/.env; };
    export $(egrep -v '^#' docker/.env | xargs)

    CMD='CHANGED_FILES="'${CHANGED_FILES}'" &&
        echo -e "\033[1;33mrun syntax checker\033[0m" &&
            for FILE in $CHANGED_FILES; do composer code-style:phplint $FILE || exit $?; done &&
        echo -e "\033[1;33mrun code style fixer\033[0m" &&
            composer code-style:fix $CHANGED_FILES &&
        echo -e "\033[1;33mrun code analyzer\033[0m" &&
            composer code-style:analyze $CHANGED_FILES || exit $?';

    docker-compose -p ${PROJECT_PREFIX} -f docker/docker-compose.yml run --entrypoint="" php bash -c "$CMD"

    git add ${CHANGED_FILES}
fi
