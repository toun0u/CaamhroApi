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

import java.io.EOFException;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.ObjectInputStream;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.LinkedHashSet;
import java.util.List;
import java.util.Set;

import com.thoughtworks.xstream.XStream;
import com.thoughtworks.xstream.converters.basic.AbstractBasicConverter;
import com.thoughtworks.xstream.io.xml.DomDriver;

public class XmlDocumentFormatRegistry implements DocumentFormatRegistry {
	private static final String DEFAULT_CONFIGURATION =
		"/"+ XmlDocumentFormatRegistry.class.getPackage().getName().replace('.', '/')
		+ "/document-formats.xml";

	private List/*<DocumentFormat>*/ documentFormats = new ArrayList();

	public XmlDocumentFormatRegistry() {
		InputStream configuration = getClass().getResourceAsStream(DEFAULT_CONFIGURATION);
		if (configuration == null) {
			throw new RuntimeException("missing default configuration: "+ DEFAULT_CONFIGURATION);
		}
		load(configuration);
	}

	public XmlDocumentFormatRegistry(InputStream configuration) {
		if (configuration == null) {
			throw new IllegalArgumentException("configuration is null");
		}
		load(configuration);
	}

	private void load(InputStream configuration) {
		XStream xstream = createXStream();
		try {
			ObjectInputStream in = xstream.createObjectInputStream(new InputStreamReader(configuration));
			while (true) {
				try {
					documentFormats.add((DocumentFormat) in.readObject());
				} catch (EOFException endOfFile) {
					break;
				}
			}
		} catch (Exception unexpected) {
			throw new RuntimeException("could not load registry configuration", unexpected);
		} finally {
			try {
				configuration.close();
			} catch (IOException ignored) { }
		}
	}

	private XStream createXStream() {
		XStream xstream = new XStream(new DomDriver());
		xstream.setMode(XStream.NO_REFERENCES);
		xstream.alias("document-format", DocumentFormat.class);
		xstream.aliasField("mime-type", DocumentFormat.class, "mimeType");
        xstream.aliasField("file-extension", DocumentFormat.class, "fileExtension");
		xstream.aliasField("export-filters", DocumentFormat.class, "exportFilters");
		xstream.alias("family", DocumentFamily.class);
        xstream.registerConverter(new AbstractBasicConverter(){
           public boolean canConvert(Class type) {
               return type.equals(DocumentFamily.class);
           }
           protected Object fromString(String name) {
               return DocumentFamily.getFamily(name);
           }
        });
		return xstream;
	}

	public DocumentFormat getFormatByName(String name) {
		for (Iterator it = documentFormats.iterator(); it.hasNext();) {
			DocumentFormat format = (DocumentFormat) it.next();
			if (format.getName().equals(name)) {
				return format;
			}
		}
		return null;
	}

	/**
	 * @param extension the file extension
	 * @return the DocumentFormat for this extension, or null if the extension is not mapped
	 */
	public DocumentFormat getFormatByFileExtension(String extension) {
		for (Iterator it = documentFormats.iterator(); it.hasNext();) {
			DocumentFormat format = (DocumentFormat) it.next();		
			if (format.getFileExtension().equals(extension)) {
				return format;
			}
		}
		return null;
	}

	public Set/*<DocumentFormat>*/ getExportFormats(DocumentFamily family) {
		Set availableFormats = new LinkedHashSet();
		for (Iterator it = documentFormats.iterator(); it.hasNext();) {
			DocumentFormat format = (DocumentFormat) it.next();		
			if (format.isExportableFrom(family)) {
				availableFormats.add(format);
			}
		}
		return availableFormats;
	}

	public Set/*<DocumentFormat>*/ getInputFormats() {
		Set inputFormats = new LinkedHashSet();
		for (Iterator it = documentFormats.iterator(); it.hasNext();) {
			DocumentFormat format = (DocumentFormat) it.next();		
			if (!format.isExportOnly()) {
				inputFormats.add(format);
			}
		}
		return inputFormats;
	}
}
