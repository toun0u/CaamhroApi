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

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

import com.sun.star.beans.XPropertySet;
import com.sun.star.bridge.XBridge;
import com.sun.star.bridge.XBridgeFactory;
import com.sun.star.comp.helper.Bootstrap;
import com.sun.star.connection.NoConnectException;
import com.sun.star.connection.XConnection;
import com.sun.star.connection.XConnector;
import com.sun.star.frame.XComponentLoader;
import com.sun.star.lang.EventObject;
import com.sun.star.lang.XComponent;
import com.sun.star.lang.XEventListener;
import com.sun.star.lang.XMultiComponentFactory;
import com.sun.star.ucb.XFileIdentifierConverter;
import com.sun.star.uno.UnoRuntime;
import com.sun.star.uno.XComponentContext;

public class AbstractOpenOfficeConnection implements OpenOfficeConnection, XEventListener {
    protected Log logger = LogFactory.getLog(getClass());

    private String connectionString;
    private XComponent bridgeComponent;
    private XMultiComponentFactory serviceManager;
    private XComponentContext componentContext;
    private boolean connected = false;
    private boolean expectingDisconnection = false;

    protected AbstractOpenOfficeConnection(String connectionString) {
        this.connectionString = connectionString;
    }

    public void setConnectionString(String connectionString) {
        this.connectionString = connectionString;
    }

    public synchronized void connect() throws ConnectException {
        logger.debug("connecting");
        try {
            XComponentContext localContext = Bootstrap.createInitialComponentContext(null);
            XMultiComponentFactory localServiceManager = localContext.getServiceManager();
            XConnector connector = (XConnector) UnoRuntime.queryInterface(XConnector.class, 
                    localServiceManager.createInstanceWithContext("com.sun.star.connection.Connector", localContext));
            XConnection connection = connector.connect(connectionString);
            XBridgeFactory bridgeFactory = (XBridgeFactory) UnoRuntime.queryInterface(XBridgeFactory.class,
                    localServiceManager.createInstanceWithContext("com.sun.star.bridge.BridgeFactory", localContext));
            XBridge bridge = bridgeFactory.createBridge("", "urp", connection, null);
            bridgeComponent = (XComponent) UnoRuntime.queryInterface(XComponent.class, bridge);
            bridgeComponent.addEventListener(this);
            serviceManager = (XMultiComponentFactory) UnoRuntime.queryInterface(XMultiComponentFactory.class,
                    bridge.getInstance("StarOffice.ServiceManager"));
            XPropertySet properties = (XPropertySet) UnoRuntime.queryInterface(XPropertySet.class, serviceManager);
            componentContext = (XComponentContext) UnoRuntime.queryInterface(XComponentContext.class,
                    properties.getPropertyValue("DefaultContext"));
            connected = true;
            logger.info("connected");
        } catch (NoConnectException connectException) {
            throw new ConnectException("connection failed: "+ connectionString +": "+ connectException.getMessage());
        } catch (Throwable otherException) {
            throw new OpenOfficeException("connection failed: "+ connectionString, otherException);
        }
    }

    public synchronized void disconnect() {
        logger.debug("disconnecting");
        expectingDisconnection = true;
        bridgeComponent.dispose();
    }

    public void disposing(EventObject event) {
        connected = false;
        if (expectingDisconnection) {
            logger.info("disconnected");
        } else {
            logger.error("disconnected unexpectedly");
        }        
        expectingDisconnection = false;
    }

    private Object getService(String className) {
        try {
            if (!connected) {
                logger.debug("trying to (re)connect");
                connect();
            }
            return serviceManager.createInstanceWithContext(className, componentContext);
        } catch (Throwable anyException) {
            throw new OpenOfficeException("could not obtain service: " + className, anyException);
        }
    }

    public XComponentLoader getDesktop() {
        return (XComponentLoader) UnoRuntime.queryInterface(XComponentLoader.class,
                getService("com.sun.star.frame.Desktop"));
    }

    public XFileIdentifierConverter getFileContentProvider() {
        return (XFileIdentifierConverter) UnoRuntime.queryInterface(XFileIdentifierConverter.class,
                getService("com.sun.star.ucb.FileContentProvider"));
    }
}
