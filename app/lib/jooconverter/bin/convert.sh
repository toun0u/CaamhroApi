#!/bin/sh

# find home - adapted from Apache ANT
if [ -z "$JOOCONVERTER_HOME" -o ! -d "$JOOCONVERTER_HOME" ]; then
  PRG="$0"
  progname=`basename "$0"`

  while [ -h "$PRG" ] ; do
    ls=`ls -ld "$PRG"`
    link=`expr "$ls" : '.*-> \(.*\)$'`
    if expr "$link" : '/.*' > /dev/null; then
      PRG="$link"
    else
      PRG=`dirname "$PRG"`"/$link"
    fi
  done

  JOOCONVERTER_HOME=`dirname "$PRG"`/..
  JOOCONVERTER_HOME=`cd "$JOOCONVERTER_HOME" && pwd`
fi

CLASSPATH="$JOOCONVERTER_HOME"/classes
for JAR in "$JOOCONVERTER_HOME"/lib/*.jar; do
  CLASSPATH="$CLASSPATH:$JAR"
done

java -classpath "$CLASSPATH" net.sf.jooreports.tools.ConvertDocument $@
