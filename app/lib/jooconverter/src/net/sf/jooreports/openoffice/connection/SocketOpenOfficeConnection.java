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
package net.sf.jooreports.openoffice.connection;

public class SocketOpenOfficeConnection extends AbstractOpenOfficeConnection {
    public static final int DEFAULT_PORT = 8100;

    public SocketOpenOfficeConnection() {
        this(DEFAULT_PORT);
    }

    public SocketOpenOfficeConnection(int port) {
        super("socket,host=localhost,port="+ port +",tcpNoDelay=1");
    }
}
