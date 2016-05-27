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
package net.sf.jooreports.converter;

import java.net.ConnectException;

import net.sf.jooreports.converter.DocumentConverter;
import net.sf.jooreports.openoffice.connection.OpenOfficeConnection;
import net.sf.jooreports.openoffice.connection.SocketOpenOfficeConnection;
import net.sf.jooreports.openoffice.converter.OpenOfficeDocumentConverter;

public class TestContext {
    private static final TestContext context = new TestContext();

    private OpenOfficeConnection connection;
    private DocumentConverter converter;

    private TestContext() {
        connection = new SocketOpenOfficeConnection(8100);
        try {
            connection.connect();
        } catch (ConnectException exception) {
            throw new RuntimeException("start OpenOffice.org on port 8100", exception);
        }
        converter = new OpenOfficeDocumentConverter(connection);
        Runtime.getRuntime().addShutdownHook(new Thread() {
           public void run() {
               connection.disconnect();
           }
        });
    }

    public static synchronized TestContext getContext() {
        return context;
    }

    public OpenOfficeConnection getOpenOfficeConnection() {
        return connection;
    }

    public DocumentConverter getDocumentConverter() {
        return converter;
    }
}
