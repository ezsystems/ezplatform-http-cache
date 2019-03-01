#!/bin/bash

if [ "$EZPLATFORM_BRANCH" = "" ] ; then
    EZPLATFORM_BRANCH=`php -r 'echo json_decode(file_get_contents("./composer.json"))->extra->_ezplatform_branch_for_behat_tests;'`
    EZPLATFORM_BRANCH="${EZPLATFORM_BRANCH:-master}"
else
    # For 1.13 script below
    git fetch --unshallow && git checkout -b tmp_ci_branch
fi
PACKAGE_BUILD_DIR=$PWD
EZPLATFORM_BUILD_DIR=${HOME}/build/ezplatform

echo "> Cloning ezsystems/ezplatform:${EZPLATFORM_BRANCH}"
git clone --depth 1 --single-branch --branch "$EZPLATFORM_BRANCH" https://github.com/ezsystems/ezplatform.git ${EZPLATFORM_BUILD_DIR}
cd ${EZPLATFORM_BUILD_DIR}

# Install everything needed for behat testing
if [ "$EZPLATFORM_BRANCH" = "1.13" ] ; then
    export RUN_INSTALL=1
    ./bin/.travis/trusty/setup_from_external_repo.sh $PACKAGE_BUILD_DIR "ezsystems/ezplatform-http-cache:dev-tmp_ci_branch"
else
    /bin/bash ./bin/.travis/trusty/setup_ezplatform.sh "${COMPOSE_FILE}" '' "${PACKAGE_BUILD_DIR}"
fi
