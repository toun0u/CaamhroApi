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

/**
 * All known input formats should be exportable to PDF 
 */
public class PdfConversionTest extends AbstractConverterTest {

	private File convert(File inputFile) throws IOException {
		File pdfFile = convert(inputFile, "pdf");
		assertFileCreated(pdfFile);
		return pdfFile;
	}

	public void testOdtToPdf() throws IOException {
		File inputFile = new File("test-data/hello.odt");
		File pdfFile = convert(inputFile);
		assertEquals("pdf content", "Hello from an OpenDocument Text!", DocumentTestUtils.readPdfText(pdfFile));
	}

	public void testSxwToPdf() throws IOException {
		File inputFile = new File("test-data/hello.sxw");
		File pdfFile = convert(inputFile);
		assertEquals("pdf content", "Hello from an OpenOffice.org 1.0 Text Document!", DocumentTestUtils.readPdfText(pdfFile));		
	}

	public void testDocToPdf() throws IOException {
		File inputFile = new File("test-data/hello.doc");
		File pdfFile = convert(inputFile);
		assertEquals("pdf content", "Hello from a Microsoft Word Document!", DocumentTestUtils.readPdfText(pdfFile));		
	}

	public void testRtfToPdf() throws IOException {
		File inputFile = new File("test-data/hello.rtf");
		File pdfFile = convert(inputFile);
		assertEquals("pdf content", "Hello from a RTF Document!", DocumentTestUtils.readPdfText(pdfFile));
	}

	public void testOdsToPdf() throws IOException {
		File inputFile = new File("test-data/hello.ods");
		File pdfFile = convert(inputFile);
		assertTrue("pdf content", DocumentTestUtils.readPdfText(pdfFile).contains("Hello from an OpenDocument Spreadsheet!"));		
	}

	public void testSxcToPdf() throws IOException {
		File inputFile = new File("test-data/hello.sxc");
		File pdfFile = convert(inputFile);
		assertTrue("pdf content", DocumentTestUtils.readPdfText(pdfFile).contains("Hello from an OpenOffice.org 1.0 Spreadsheet!"));		
	}

	public void testXlsToPdf() throws IOException {
		File inputFile = new File("test-data/hello.xls");
		File pdfFile = convert(inputFile);
		assertTrue("pdf content", DocumentTestUtils.readPdfText(pdfFile).contains("Hello from a Microsoft Excel Spreadsheet!"));		
	}

	public void testOdpToPdf() throws IOException {
		File inputFile = new File("test-data/hello.odp");
		File pdfFile = convert(inputFile);
		assertEquals("pdf content", "Hello from an OpenDocument Presentation!", DocumentTestUtils.readPdfText(pdfFile));		
	}

	public void testSxiToPdf() throws IOException {
		File inputFile = new File("test-data/hello.sxi");
		File pdfFile = convert(inputFile);
		assertEquals("pdf content", "Hello from an OpenOffice.org 1.0 Presentation!", DocumentTestUtils.readPdfText(pdfFile));		
	}

	public void testPptToPdf() throws IOException {
		File inputFile = new File("test-data/hello.ppt");
		File pdfFile = convert(inputFile);
		assertEquals("pdf content", "Hello from a Microsoft PowerPoint Presentation!", DocumentTestUtils.readPdfText(pdfFile));		
	}
}
