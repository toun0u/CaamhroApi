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
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.StringWriter;

import org.pdfbox.pdmodel.PDDocument;
import org.pdfbox.util.PDFTextStripper;

public class DocumentTestUtils {
    private static final int BYTE_ORDER_MARK_CHAR = 0xFEFF;

    /**
     * Extracts the text from a PDF file.
     * 
     * @param pdfFile
     * @return the document content as plan text
     * @throws IOException
     */
    public static String readPdfText(File pdfFile) throws IOException {
        PDFTextStripper textStripper = new PDFTextStripper();
        PDDocument document = PDDocument.load(pdfFile);
        String text = textStripper.getText(document);
        document.close();
        return text.trim();
    }

    public static String readContent(File file) throws IOException {
        char[] buffer = new char[2048];
        InputStreamReader reader = new InputStreamReader(new FileInputStream(file), "UTF-8");
        StringWriter writer = new StringWriter();
        int length;
        while ((length = reader.read(buffer, 0, 2048)) != -1) {
            writer.write(buffer, 0, length);
        }
        String content = stripByteOrderMarkChar(writer.toString());
        return content.trim();
    }

    private static String stripByteOrderMarkChar(String content) {        
        if (content.length() > 0 && content.charAt(0) == BYTE_ORDER_MARK_CHAR) {
            return content.substring(1).trim();
        }
        return content;
    }
}
