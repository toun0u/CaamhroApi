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
package net.sf.jooreports.openoffice.converter;

import java.io.File;

import net.sf.jooreports.converter.DocumentConverter;
import net.sf.jooreports.converter.DocumentFormat;
import net.sf.jooreports.converter.DocumentFormatRegistry;
import net.sf.jooreports.converter.XmlDocumentFormatRegistry;
import net.sf.jooreports.openoffice.connection.OpenOfficeConnection;
import net.sf.jooreports.openoffice.connection.OpenOfficeException;

import com.sun.star.beans.PropertyValue;
import com.sun.star.frame.XComponentLoader;
import com.sun.star.frame.XStorable;
import com.sun.star.lang.XComponent;
import com.sun.star.ucb.XFileIdentifierConverter;
import com.sun.star.uno.UnoRuntime;


public class OpenOfficeDocumentConverter implements DocumentConverter {
	private static final PropertyValue[] HIDDEN = new PropertyValue[] { OpenOfficeDocumentConverter.property("Hidden", Boolean.TRUE) };
	private OpenOfficeConnection connection;
	private DocumentFormatRegistry formatRegistry;

	public OpenOfficeDocumentConverter(OpenOfficeConnection connection) {
		this(connection, new XmlDocumentFormatRegistry());
	}

	public OpenOfficeDocumentConverter(OpenOfficeConnection connection, DocumentFormatRegistry formatRegistry) {
		this.connection = connection;
		this.formatRegistry = formatRegistry;
	}

	private static String fileExtension(File file) {
		String name = file.getName();
		int dot = name.lastIndexOf(".");
		if (dot != -1) {
			return name.substring(dot + 1, name.length());
		}
		return null;
	}

	/**
	 * Convert a document. Input and output formats are determined by the file extension.
	 * 
	 * @param inputFile
	 * @param outputFile
	 */
	public void convert(File inputFile, File outputFile) {
		DocumentFormat outputFormat = formatRegistry.getFormatByFileExtension(fileExtension(outputFile));
        if (outputFormat == null) {
            throw new IllegalArgumentException("unknown output format for extension: "+ fileExtension(outputFile));
        }
		convert(inputFile, outputFile, outputFormat);
	}

	/**
	 * Convert a document. The input format (for validation) is guessed from the file extension.
	 * 
	 * @param inputFile
	 * @param outputFile
	 */
	public void convert(File inputFile, File outputFile, DocumentFormat outputFormat) {
		DocumentFormat inputFormat = formatRegistry.getFormatByFileExtension(fileExtension(inputFile));
		convert(inputFile, inputFormat, outputFile, outputFormat);
	}

	/**
	 * Convert a document. Input and output formats are explicitly passed as arguments.
	 * 
	 * @param inputFile
	 * @param inputFormat
	 * @param outputFile
	 * @param outputFormat
	 */
	public void convert(File inputFile, DocumentFormat inputFormat, File outputFile, DocumentFormat outputFormat) {
		if (inputFile == null || !inputFile.canRead()) {
			throw new IllegalArgumentException("invalid input file: "+ inputFile);
		}
		if (outputFile == null) {
			//TODO how to check that we have write permission?
			throw new IllegalArgumentException("invalid output file: "+ outputFile);
		}
		if (inputFormat == null || inputFormat.isExportOnly()) {
			throw new IllegalArgumentException("unsupported input format: "+ inputFile);
		}
		if (outputFormat == null || !outputFormat.isExportableFrom(inputFormat.getFamily())) {
			throw new IllegalArgumentException("unsupported conversion: from "+ inputFormat.getName() +" to "+ outputFormat.getName());
		}
		synchronized (connection) {
			XFileIdentifierConverter fileContentProvider = connection.getFileContentProvider();
			String inputUrl = fileContentProvider.getFileURLFromSystemPath("", inputFile.getAbsolutePath());
			String outputUrl = fileContentProvider.getFileURLFromSystemPath("", outputFile.getAbsolutePath());
			String filterName = outputFormat.getExportFilter(inputFormat.getFamily());
			try {
				loadAndExport(inputUrl, outputUrl, new PropertyValue[] { OpenOfficeDocumentConverter.property("FilterName", filterName) });
			} catch (Throwable throwable) {
				throw new OpenOfficeException("conversion failed", throwable);
			}
		}		
	}

	private void loadAndExport(String inputUrl, String outputUrl, PropertyValue[] exportProperties) throws Exception {
		XComponentLoader desktop = connection.getDesktop();
		XComponent document = desktop.loadComponentFromURL(inputUrl, "_blank", 0, HIDDEN);
		if (document == null) {
			throw new OpenOfficeException("could not load document "+ inputUrl);
		}
		try {
			XStorable storable = (XStorable) UnoRuntime.queryInterface(XStorable.class, document);
			storable.storeToURL(outputUrl, exportProperties);
		} finally {
			document.dispose();
		}
	}

    private static PropertyValue property(String name, Object value) {
    	PropertyValue property = new PropertyValue();
    	property.Name = name;
    	property.Value = value;
    	return property;
    }
}
