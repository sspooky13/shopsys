#!/bin/bash

DIRECTORY=$1

if [ "$#" -eq 0 ]
then
 echo "Missing directory argument"
 exit 1
fi

CHECK_RESULT_CODE_TEXT=`vendor/bin/composer-require-checker check $DIRECTORY/composer.json --ignore-parse-errors`
CHECK_RESULT_CODE=$?
if [ "$CHECK_RESULT_CODE" -eq "0" ]
then
 exit 0
fi

SYMBOLS_FOUND=`echo "$CHECK_RESULT_CODE_TEXT" | tail -n '+6' | head -n '-1' | sed 's/| //' | sed 's/ .*$//' | sort`

IGNORE_FILE=$DIRECTORY/composer-check-dependencies-ignore.txt

if [ -f $IGNORE_FILE ]
then
 IGNORED_SYMBOLS_NOT_PRESENT=`echo "$SYMBOLS_FOUND" | comm -23 $IGNORE_FILE -`
 if [ "$IGNORED_SYMBOLS_NOT_PRESENT" != "" ]
 then
  echo "Following symbols are ignored, but not present in project:"
  echo ""
  echo "$IGNORED_SYMBOLS_NOT_PRESENT"
  exit 1
 fi

 SYMBOLS_NOT_IGNORED=`echo "$SYMBOLS_FOUND" | comm -23 - $IGNORE_FILE`

 if [ "$SYMBOLS_NOT_IGNORED" == "" ]
 then
  exit 0
 fi
 SYMBOLS_FOUND=$SYMBOLS_NOT_IGNORED
fi

echo "Following unknown symbols were found:"
echo ""
echo "$SYMBOLS_FOUND"
exit $CHECK_RESULT_CODE
