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

public class PipeOpenOfficeConnection extends AbstractOpenOfficeConnection {
    public static final String DEFAULT_PIPE_NAME = "jooreports";

    public PipeOpenOfficeConnection() {
        this(DEFAULT_PIPE_NAME);
    }

    public PipeOpenOfficeConnection(String pipeName) {
        super("pipe,name="+ pipeName);
    }
}
