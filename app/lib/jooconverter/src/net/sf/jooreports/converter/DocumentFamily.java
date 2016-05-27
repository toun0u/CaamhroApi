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

/**
 * Enum-style class declaring the available document families (Text, Spreadsheet, Presentation).
 */
public class DocumentFamily {
	public static final DocumentFamily TEXT = new DocumentFamily("Text");
	public static final DocumentFamily SPREADSHEET = new DocumentFamily("Spreadsheet");
	public static final DocumentFamily PRESENTATION = new DocumentFamily("Presentation");
    private static DocumentFamily[] families = new DocumentFamily[] { TEXT, SPREADSHEET, PRESENTATION };

    private String name;

	private DocumentFamily(String name) {
		this.name = name;
	}

    public static DocumentFamily getFamily(String name) {
        for (int i = 0; i < families.length; i++) {
            if (families[i].name.equals(name)) {
                return families[i];
            }
        }
        return null;
    }

    public String toString() {
        return name;
    }
}
