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

public class PresentationConversionTest extends AbstractConverterTest {

	public void testOdpToPpt() throws IOException {
		File inputFile = new File("test-data/hello.odp");
		File outputFile = convert(inputFile, "ppt");
		assertFileCreated(outputFile);
	}

	public void testOdpToSxi() throws IOException {
		File inputFile = new File("test-data/hello.odp");
		File outputFile = convert(inputFile, "sxi");
		assertFileCreated(outputFile);
	}

	public void testOdpToSwf() throws IOException {
		File inputFile = new File("test-data/hello.odp");
		File outputFile = convert(inputFile, "swf");
		assertFileCreated(outputFile);
	}

	public void testPptToOdp() throws IOException {
		File inputFile = new File("test-data/hello.ppt");
		File outputFile = convert(inputFile, "odp");
		assertFileCreated(outputFile);
	}
}
