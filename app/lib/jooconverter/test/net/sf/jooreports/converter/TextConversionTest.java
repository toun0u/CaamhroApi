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

public class TextConversionTest extends AbstractConverterTest {

	public void testOdtToTxt() throws IOException {
		File inputFile = new File("test-data/hello.odt");
		File outputFile = convert(inputFile, "txt");
		assertFileCreated(outputFile);
		assertEquals("output content", "Hello from an OpenDocument Text!", DocumentTestUtils.readContent(outputFile));
	}

	public void testOdtToDoc() throws IOException {
		File inputFile = new File("test-data/hello.odt");
		File outputFile = convert(inputFile, "doc");
		assertFileCreated(outputFile);
	}

	public void testOdtToRtf() throws IOException {
		File inputFile = new File("test-data/hello.odt");
		File outputFile = convert(inputFile, "rtf");
		assertFileCreated(outputFile);
	}

	public void testOdtToSxw() throws IOException {
		File inputFile = new File("test-data/hello.odt");
		File outputFile = convert(inputFile, "sxw");
		assertFileCreated(outputFile);
	}

	public void testDocToOdt() throws IOException {
		File inputFile = new File("test-data/hello.doc");
		File outputFile = convert(inputFile, "odt");
		assertFileCreated(outputFile);
	}
}
