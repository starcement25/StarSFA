package com.forcepower.acedns.util;

import android.util.Log;

import java.io.*;
import java.util.zip.ZipEntry;
import java.util.zip.ZipOutputStream;

public class FolderCompressionTask {

    public static boolean zipFile(String sourcePath, String outputFile) {
        final int BUFFER = 2048;
        ZipOutputStream out = null;
        try {
            File sourceFile = new File(sourcePath);
            if (!sourceFile.exists()) {
                Log.e("ZIP", "Source path does not exist: " + sourcePath);
                return false;
            }

            FileOutputStream dest = new FileOutputStream(outputFile);
            out = new ZipOutputStream(new BufferedOutputStream(dest));

            if (sourceFile.isDirectory()) {
                zipSubFolder(out, sourceFile, sourceFile.getAbsolutePath().length() + 1);
            } else {
                zipSingleFile(out, sourceFile, "");
            }

            out.flush();
            out.close();
            Log.i("ZIP", "Zip file created: " + outputFile);
            return true;

        } catch (Exception e) {
            e.printStackTrace();
            try {
                if (out != null) out.close();
            } catch (IOException ignored) {}
            return false;
        }
    }

    private static void zipSubFolder(ZipOutputStream out, File folder, int basePathLength) throws IOException {
        final int BUFFER = 2048;
        byte[] data = new byte[BUFFER];

        File[] fileList = folder.listFiles();
        if (fileList == null) return;

        for (File file : fileList) {
            if (file.isDirectory()) {
                zipSubFolder(out, file, basePathLength);
            } else {
                String unmodifiedFilePath = file.getAbsolutePath();
                String relativePath = unmodifiedFilePath.substring(basePathLength);

                Log.i("ZIP", "Adding file: " + relativePath);

                FileInputStream fi = new FileInputStream(unmodifiedFilePath);
                BufferedInputStream origin = new BufferedInputStream(fi, BUFFER);
                ZipEntry entry = new ZipEntry(relativePath.replace("\\", "/"));
                out.putNextEntry(entry);

                int count;
                while ((count = origin.read(data, 0, BUFFER)) != -1) {
                    out.write(data, 0, count);
                }

                origin.close();
                out.closeEntry(); // ✅ Critical for Windows ZIP reader
            }
        }
    }

    private static void zipSingleFile(ZipOutputStream out, File file, String parentFolder) throws IOException {
        final int BUFFER = 2048;
        byte[] data = new byte[BUFFER];

        String entryName = parentFolder + file.getName();
        FileInputStream fi = new FileInputStream(file);
        BufferedInputStream origin = new BufferedInputStream(fi, BUFFER);

        ZipEntry entry = new ZipEntry(entryName);
        out.putNextEntry(entry);

        int count;
        while ((count = origin.read(data, 0, BUFFER)) != -1) {
            out.write(data, 0, count);
        }

        origin.close();
        out.closeEntry(); // ✅ Very important
    }
}
