//
// JooReports - A new generation of dynamic office documents
// Copyright (C) 2005 - Mirko Nasato <mirko@artofsolving.com>
//
// This library is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or (at your option) any later version.
//
// This library is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// Lesser General Public License for more details.
// http://www.gnu.org/copyleft/lesser.html
//
package net.sf.jooreports.tools;

import java.io.File;
import java.net.ConnectException;

import net.sf.jooreports.converter.DocumentConverter;
import net.sf.jooreports.openoffice.connection.OpenOfficeConnection;
import net.sf.jooreports.openoffice.connection.SocketOpenOfficeConnection;
import net.sf.jooreports.openoffice.converter.OpenOfficeDocumentConverter;

/**
 * Command line tool to convert a document into a different format. Formats are guessed from file extensions.
 */
public class ConvertDocument {

    public static void main(String[] args) throws Exception {
        if (args.length < 2) {
            System.err.println("USAGE: "+ ConvertDocument.class.getName() +" <input-file> <output-file>");
            System.exit(255);
        }
        File inputFile = new File(args[0]);
        File outputFile = new File(args[1]);
        OpenOfficeConnection connection = new SocketOpenOfficeConnection();
        try {
            connection.connect();
        } catch (ConnectException officeNotRunning) {
            System.err.println("ERROR: connection failed. Please make sure OpenOffice.org is running and listening on port "+ SocketOpenOfficeConnection.DEFAULT_PORT +".");
            System.exit(1);
        }
        try {
            DocumentConverter converter = new OpenOfficeDocumentConverter(connection);
            converter.convert(inputFile, outputFile);
        } finally {
            connection.disconnect();
        }
    }
}
