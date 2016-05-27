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

import java.net.ConnectException;

import com.sun.star.frame.XComponentLoader;
import com.sun.star.ucb.XFileIdentifierConverter;

/**
 * A UNO remote protocol connection to a listening OpenOffice.org instance 
 */
public interface OpenOfficeConnection {

	public void connect() throws ConnectException;

	public void disconnect();

	/**
	 * @return the com.sun.star.frame.Desktop service
	 */
	public XComponentLoader getDesktop();

	/**
	 * @return the com.sun.star.ucb.FileContentProvider service
	 */
	public XFileIdentifierConverter getFileContentProvider();

}
