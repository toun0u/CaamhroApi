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
package net.sf.jooreports.io;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

public class InputOutputUtils {
    private static final int BUFFER_SIZE = 4096;

    public static void copy(InputStream input, OutputStream output) throws IOException {
        byte[] buffer = new byte[BUFFER_SIZE];
        while (true) {
            int length = input.read(buffer, 0, BUFFER_SIZE);
            if (length == -1) break;
            output.write(buffer, 0, length);
        }
    }

    public static String getFileExtension(String fileName) {
        int dot = fileName.lastIndexOf('.');
        return fileName.substring(dot + 1);
    }
}
