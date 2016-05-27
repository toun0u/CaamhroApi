package net.sf.jooreports.converter.web;

import javax.servlet.ServletContext;
import javax.servlet.ServletContextEvent;
import javax.servlet.ServletContextListener;

import net.sf.jooreports.converter.DocumentConverter;
import net.sf.jooreports.converter.DocumentFormatRegistry;
import net.sf.jooreports.converter.XmlDocumentFormatRegistry;
import net.sf.jooreports.openoffice.connection.OpenOfficeConnection;
import net.sf.jooreports.openoffice.connection.SocketOpenOfficeConnection;
import net.sf.jooreports.openoffice.converter.OpenOfficeDocumentConverter;

import org.apache.commons.fileupload.DiskFileUpload;

public class WebAppContext implements ServletContextListener {
    private static int OPENOFFICE_PORT = 8100;
    private static long MAX_UPLOAD_SIZE = 2 * 1024 * 1024;  // 2Mb

    private OpenOfficeConnection openOfficeConnection;
    private DocumentFormatRegistry documentFormatRegistry;
    private DocumentConverter documentConverter;
    
    private DiskFileUpload fileUpload;

    public static WebAppContext getWebAppContext(ServletContext servletContext) {
        return (WebAppContext) servletContext.getAttribute(WebAppContext.class.getName());
    }

    public DocumentFormatRegistry getDocumentFormatRegistry() {
        return documentFormatRegistry; 
    }

    public DocumentConverter getDocumentConverter() {
        return documentConverter;
    }

    public DiskFileUpload getFileUpload() {
        return fileUpload;
    }

    private void start() throws Exception {
        openOfficeConnection = new SocketOpenOfficeConnection(OPENOFFICE_PORT);
        openOfficeConnection.connect();
        documentFormatRegistry = new XmlDocumentFormatRegistry();
        documentConverter = new OpenOfficeDocumentConverter(openOfficeConnection, documentFormatRegistry);
        
        fileUpload = new DiskFileUpload();
        fileUpload.setSizeMax(MAX_UPLOAD_SIZE);
    }

    private void stop() {
        if (openOfficeConnection != null) {
            openOfficeConnection.disconnect();
        }
    }

    public void contextInitialized(ServletContextEvent event) {
        WebAppContext webAppContext = new WebAppContext();
        event.getServletContext().setAttribute(WebAppContext.class.getName(), webAppContext);
        try {
            webAppContext.start();
        } catch (Throwable anyException) {
            throw new RuntimeException("could not initialize webapp context", anyException);
        }
    }

    public void contextDestroyed(ServletContextEvent event) {
        WebAppContext webAppContext = (WebAppContext) event.getServletContext().getAttribute(WebAppContext.class.getName());
        webAppContext.stop();
    }
}
