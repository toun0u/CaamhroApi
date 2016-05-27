JOOConverter
============

This is JOOConverter version 2.0rc2, released on 2005-10-30.

JOOConverter is a Java library for converting office documents into different
formats, using OpenOffice.org 2.0.

Before you can perform any conversions you need to start OpenOffice.org
in listening mode on port 8100 as described in this document

http://api.openoffice.org/docs/DevelopersGuide/FirstSteps/FirstSteps.htm#1+3+3+3+Make+the+office+listen

As a quick start you can type from a command line 

  soffice -headless -accept="socket,port=8100;urp;"

JOOConverter is mainly a Java library but also includes a couple of ready-to-use tools:

 * a web application that you can deploy into any servlet container (e.g. Apache Tomcat)
 * a command line tool (java -jar jooconverter.jar <input-document> <output-document>)

JOOConverter is part of JOOReports: http://jooreports.sourceforge.net

Licenses
========

JOOConverter is distributed under the terms of the LGPL.

This basically means that you are free to use it in both open source
and commercial projects.

If you modify the library itself you are required to contribute
your changes back, so JOOConverter can be improved.

(You are free to modify the sample webapp as a starting point for your
own webapp without restrictions.)

JOOConverter includes the following third-party libraries so you must
agree to their respective licenses as well

 * OpenOffice.org Java/UNO bridge - http://www.openoffice.org
   LGPL

 * XStream - http://xstream.codehaus.org
   BSD-style (revised) 

 * Jakarta Commons logging and fileupload
   Apache License 2.0

 * PDFBox - http://www.pdfbox.org
   optional (used by unit tests) - BSD-style (revised)

Third-party licenses are included in lib/licenses.

In compliance to some of the above licenses I also need to state here
that JOOConverter includes software developed by

 * the Apache Software Foundation (http://www.apache.org)

-- Mirko Nasato (mirko at artofsolving.com).
