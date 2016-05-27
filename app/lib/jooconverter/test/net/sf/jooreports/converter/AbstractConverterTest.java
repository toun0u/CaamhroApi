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
import java.io.IOException;

import junit.framework.TestCase;
import net.sf.jooreports.converter.DocumentConverter;

public abstract class AbstractConverterTest extends TestCase {
    private TestContext context = TestContext.getContext();
    protected DocumentConverter converter = context.getDocumentConverter();

    protected static File createTempFile(String extension) throws IOException {
        File tempFile = File.createTempFile("document", "."+ extension);
        tempFile.deleteOnExit();
        return tempFile;
    }

    protected File convert(File inputFile, String extension) throws IOException {
        File outputFile = createTempFile(extension);
        converter.convert(inputFile, outputFile);
        return outputFile;
    }

    protected static void assertFileCreated(File file) {
        assertTrue("file created", file.isFile() && file.length() > 0);
    }
}
