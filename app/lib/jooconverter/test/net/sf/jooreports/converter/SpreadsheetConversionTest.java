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

public class SpreadsheetConversionTest extends AbstractConverterTest {

	public void testOdsToXls() throws IOException {
		File inputFile = new File("test-data/hello.ods");
		File outputFile = convert(inputFile, "xls");
		assertFileCreated(outputFile);
	}

	public void testOdsToSxc() throws IOException {
		File inputFile = new File("test-data/hello.ods");
		File outputFile = convert(inputFile, "sxc");
		assertFileCreated(outputFile);
	}

	public void testXlsToOds() throws IOException {
		File inputFile = new File("test-data/hello.xls");
		File outputFile = convert(inputFile, "ods");
		assertFileCreated(outputFile);
	}
}
