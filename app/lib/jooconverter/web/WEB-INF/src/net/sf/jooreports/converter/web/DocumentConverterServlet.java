package net.sf.jooreports.converter.web;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.util.Iterator;
import java.util.List;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.apache.commons.fileupload.DiskFileUpload;
import org.apache.commons.fileupload.FileItem;
import org.apache.commons.fileupload.FileUpload;
import org.apache.commons.fileupload.FileUploadException;

import net.sf.jooreports.converter.DocumentConverter;
import net.sf.jooreports.converter.DocumentFormat;
import net.sf.jooreports.converter.DocumentFormatRegistry;

public class DocumentConverterServlet extends HttpServlet {
    private static final int RESPONSE_BUFFER_SIZE = 4096;

    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws IOException, ServletException {
        if (!FileUpload.isMultipartContent(request)) {
            throw new IllegalArgumentException("request is not multipart");
        }
        WebAppContext webAppContext = WebAppContext.getWebAppContext(getServletContext());
        DocumentConverter converter = webAppContext.getDocumentConverter();
        DocumentFormatRegistry registry = webAppContext.getDocumentFormatRegistry();

        // determine output format based on the request uri
        String outputExtension = fileExtension(request.getRequestURI());
        DocumentFormat outputFormat = registry.getFormatByFileExtension(outputExtension);
        if (outputFormat == null) {
            throw new IllegalArgumentException("invalid outputFormat: "+ outputExtension);
        }

        FileItem inputFileUpload = getInputFileUpload(request, webAppContext);
        if (inputFileUpload == null) {
            throw new IllegalArgumentException("inputDocument is null");
        }

        File inputFile = null;
        File outputFile = null;
        try {
            inputFile = File.createTempFile("document", "."+ fileExtension(inputFileUpload.getName()));
            inputFileUpload.write(inputFile);
            outputFile = File.createTempFile("document", "."+ outputFormat.getFileExtension());
            converter.convert(inputFile, outputFile, outputFormat);
            response.setContentType(outputFormat.getMimeType());
            String fileName = fileNameWithoutExtension(inputFileUpload.getName()) +"."+ outputFormat.getFileExtension();
            response.setHeader("Content-Disposition", "inline; filename="+ fileName);
            sendFile(response, outputFile);
        } catch (Exception e) {
            throw new ServletException("conversion failed", e);
        } finally {
            if (inputFile != null) inputFile.delete();
            if (outputFile != null) outputFile.delete();
        }
    }

    private FileItem getInputFileUpload(HttpServletRequest request, WebAppContext webAppContext) throws ServletException {
        FileItem inputFileUpload = null;
        try {
            DiskFileUpload upload = webAppContext.getFileUpload();
            List fileItems = upload.parseRequest(request);
            for (Iterator it = fileItems.iterator(); it.hasNext();) {
                FileItem item = (FileItem) it.next();
                if ("inputDocument".equals(item.getFieldName())) {
                    inputFileUpload = item;
                }
            }
        } catch (FileUploadException e) {
            throw new ServletException("file upload failed", e);
        }
        return inputFileUpload;
    }

    private String fileExtension(String fileName) {
        int dot = fileName.lastIndexOf('.');
        return fileName.substring(dot + 1);
    }

    private String fileNameWithoutExtension(String fileName) {
        int slash = fileName.lastIndexOf('/');
        int dot = fileName.lastIndexOf('.');
        return fileName.substring(slash + 1, dot);
    }

    private void sendFile(HttpServletResponse response, File file) throws IOException {
        response.setContentLength((int) file.length());
        OutputStream out = response.getOutputStream();
        FileInputStream in = new FileInputStream(file);
        byte[] buffer = new byte[RESPONSE_BUFFER_SIZE];
        int bytes;
        while ((bytes = in.read(buffer, 0, RESPONSE_BUFFER_SIZE)) != -1) {
            out.write(buffer, 0, bytes);
        }
        in.close();
    }
}
