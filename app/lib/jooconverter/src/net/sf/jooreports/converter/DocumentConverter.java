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

import java.io.File;

public interface DocumentConverter {

    /**
     * Convert a document.
     * 
     * @param inputFile
     * @param inputFormat
     * @param outputFile
     * @param outputFormat
     */
    public void convert(File inputFile, DocumentFormat inputFormat, File outputFile, DocumentFormat outputFormat);


    /**
     * Convert a document. The input format is guessed from the file extension.
     * 
     * @param inputDocument
     * @param outputDocument
     * @param outputFormat
     */
    public void convert(File inputDocument, File outputDocument, DocumentFormat outputFormat);

    /**
     * Convert a document. Both input and output formats are guessed from the file extension.
     * 
     * @param inputDocument
     * @param outputDocument
     */
    public void convert(File inputDocument, File outputDocument);

}
