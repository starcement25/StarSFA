package com.forcepower.acedns.util;

import android.bluetooth.BluetoothDevice;
import android.bluetooth.BluetoothSocket;
import android.util.Log;

import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;


class ConnectedThread extends Thread {

    //private int tempPacket[];
    //private int packetLength = 0;
    //private boolean available = false;
    //private boolean timeoutFlag = false;
/////////////////// For print variables ////////////////////
    private static final String TAG = "ConnectedThread";
    private final BluetoothSocket mmSocket;
    private final InputStream mmInStream;
    public int nofByts = 0;
    public String ComData = null;
    ;
    int sucess = 0;
    String outputtoprint = null;
    int[] packetBuffer = new int[5000];
    String ComData1 = null;
    int noBytes = 0, ct = 0, nofByts1 = 0, k = 0, totDataLen;
    byte[] fpDatabytes = null;
    byte ENROLL_ID = 0x21;
    byte ILV_OK = 0x00;
    byte ILVSTS_OK = 0x00;
    byte ISO_197942 = (byte) 0x6E;
    byte ILVSTS_HIT = 0x01;
    byte ILVSTS_NO_HIT = 0x02;
    File directory = null;
    String date = "", time = "";
    String dateStamp = new SimpleDateFormat("dd.MM.yyyy").format(Calendar.getInstance().getTime());
    String timeStamp = new SimpleDateFormat("HH:mm").format(Calendar.getInstance().getTime());
    private OutputStream mmOutStream;
    private BluetoothDevice device;

    //private BluetoothChatService btChatService;
    public ConnectedThread(BluetoothSocket socket, BluetoothDevice device, BluetoothChatService btChatService) {
        //this.btChatService=btChatService;
        Log.d(TAG, "create ConnectedThread");
        mmSocket = socket;
        InputStream tmpIn = null;
        OutputStream tmpOut = null;
        this.setDevice(device);
        // Get the BluetoothSocket input and output streams
        try {
            if (socket != null) {
                tmpIn = socket.getInputStream();
                tmpOut = socket.getOutputStream();

            } else {
                System.out.println("Socket is closed by the server");
            }
        } catch (IOException e) {
            Log.e(TAG, "temp sockets not created", e);
        }
        mmInStream = tmpIn;
        mmOutStream = tmpOut;
    }

    //To reset printer for finger print scan
    public void FPReset() {
        byte[] reset = new byte[8];
        reset[6] = (byte) 0x100;

        try {
            Thread.sleep(200);
            mmOutStream.write(reset);
            mmOutStream.flush();
            Thread.sleep(200);
        } catch (Exception e) {
            e.printStackTrace();
            Log.d(TAG, "IO Error");
        }
    }

    public int readImagedata(ArrayList<String> dataList) {
        try {
            printHeader();
            PrinterData(dataList.get(0), 3);
            PrinterData("          ", 1);
            PrinterData("-----------------------", 1);
            PrinterData(" Item Qty Rate Dis  Amt", 1);
            PrinterData("-----------------------", 1);
            for (int ii = 1; ii < dataList.size(); ii++) {
                String[] dataArray = dataList.get(ii).split("\\^");
                if (dataArray[0].length() > 0) {
                    PrinterData(dataArray[0], 3);
                }
                if (dataArray[1].length() > 0) {
                    PrinterData(dataArray[1], 3);
                }
            }
            printFooter();
            mmSocket.close();
        } catch (Exception e) {
            e.printStackTrace();
            Log.v("e.printStackTrace()", "readImagedata");
        }
        return sucess;


    }

    public int readImagedata(String a1, String a2, String a3, String a4, String a5, String a6) {
        String outputtoprint1 = null;
        //String outputtoprint2=null;
        String outputtoprint3 = null;
        String outputtoprint4 = null;
        String outputtoprint5 = null;
        String outputtoprint6 = null;
        String outputtoprint7 = null;
        String outputtoprint8 = null;
        String outputtoprint9 = null;
        outputtoprint1 = "Received with thanks frm";
        outputtoprint3 = a1;
        outputtoprint4 = a2;
        outputtoprint5 = a3;
        outputtoprint6 = a4;
        outputtoprint7 = a5;
        outputtoprint8 = a6;
        outputtoprint9 = "                     ";

        try {
            PrinterData(outputtoprint1, 1);
            PrinterData(outputtoprint3, 1);
            PrinterData(outputtoprint4, 1);
            PrinterData(outputtoprint6, 1);
            PrinterData(outputtoprint5, 1);
            PrinterData(outputtoprint7, 1);
            PrinterData(outputtoprint8, 1);
            PrinterData(outputtoprint9, 1);
            PrinterData(outputtoprint9, 1);
            PrinterData(outputtoprint9, 1);
            PrinterData(outputtoprint9, 1);

            mmSocket.close();

        } catch (Exception e) {
            e.printStackTrace();
            Log.v("e.printStackTrace()", "readImagedata");
        }

        return sucess;


    }

    public int readImagedata(String a1, String a2, String a3, String a4, String a5, String a6, String a7, String a8, String a9) {
        String outputtoprint1 = null;
        //String outputtoprint2=null;
        String outputtoprint3 = null;
        String outputtoprint4 = null;
        String outputtoprint5 = null;
        String outputtoprint6 = null;
        String outputtoprint7 = null;
        String outputtoprint8 = null;
        String outputtoprint9 = null;
        String outputtoprint10 = null;
        String outputtoprint11 = null;
        String outputtoprint12 = null;


        outputtoprint1 = "Received with thanks frm";
        outputtoprint3 = a1;
        outputtoprint4 = a2;
        outputtoprint5 = a3;
        outputtoprint6 = a4;
        outputtoprint7 = a5;
        outputtoprint8 = a6;
        outputtoprint9 = a7;
        outputtoprint10 = a8;
        outputtoprint11 = a9;
        outputtoprint12 = "                     ";
        try {
            PrinterData(outputtoprint1, 1);
            PrinterData(outputtoprint3, 1);
            PrinterData(outputtoprint4, 1);
            PrinterData(outputtoprint6, 1);
            PrinterData(outputtoprint5, 1);
            PrinterData(outputtoprint7, 1);
            PrinterData(outputtoprint8, 1);
            PrinterData(outputtoprint9, 1);
            PrinterData(outputtoprint10, 1);
            PrinterData(outputtoprint11, 1);
            PrinterData(outputtoprint12, 1);
            PrinterData(outputtoprint12, 1);
            PrinterData(outputtoprint12, 1);
            PrinterData(outputtoprint12, 1);

            mmSocket.close();

        } catch (Exception e) {
            e.printStackTrace();
            Log.v("e.printStackTrace()", "readImagedata");
        }

        return sucess;
    }

    public void cancel() {
        try {
            mmSocket.close();
        } catch (IOException e) {
            Log.e(TAG, "close() of connect socket failed", e);
        }
    }

    public BluetoothDevice getDevice() {
        return device;
    }

    public void setDevice(BluetoothDevice device) {
        this.device = device;
    }

    String stringToHex(String str) {
        char[] chars = str.toCharArray();
        StringBuffer strBuffer = new StringBuffer();
        for (int i = 0; i < chars.length; i++) {
            strBuffer.append(Integer.toHexString((int) chars[i]));
        }
        return strBuffer.toString();
    }

    public void ReadPrinterresp() {
        int iResp = 0;
        try {

            iResp = mmInStream.read();
            Log.v("ReadPrinterresp", iResp + "");
            if (iResp != 128 || iResp != 0) {
                switch (iResp) {
                    case 193:
                        Log.v("193", 193 + "");
                        sucess = 1;
                        break;
                }


            }
        } catch (Exception e) {
        }
    }

    private int PrinterData(String data, int Font) {
        int i = 0, j = 0, col = 0, la = 0;
        char[] tempBuffer = new char[1000];
        int[] dataBuffer = new int[5000];
        //byte[] b = new byte[2];
        System.out.println(data);
        data.getChars(0, data.length(), tempBuffer, col);
        totDataLen = data.length();
/////////////////////// PACKET FORMAT /////////////////////
        for (i = 0; la < data.length(); ) {
            if (Font == 1 || Font == 2) {
                if (Font == 1) {
                    dataBuffer[i++] = 0xf0;
                    Log.v("dataBuffer[i++]Font1", i + "");
                } else if (Font == 2)
                    dataBuffer[i++] = 0xf1;
                for (j = 0; j < 24; j++) {
                    Log.v("la", la + "");
                    Log.v("data.length()", data.length() + "");
                    if (la < data.length()) {
                        dataBuffer[i++] = tempBuffer[la++];
                    } else
                        break;
                }
            } else if (Font == 3 || Font == 4) {
                if (Font == 3)
                    dataBuffer[i++] = 0xf2;
                else if (Font == 4)
                    dataBuffer[i++] = 0xf3;
                for (j = 0; j < 42; j++) {
                    if (la < data.length()) {
                        dataBuffer[i++] = tempBuffer[la++];
                    } else
                        break;
                }
            }
        }
/////////////////////// PACKET FORMAT /////////////////////
        try {
            Log.v("SendPrint(dataBuffer, i,Font)", i + "");
            SendPrint(dataBuffer, i, Font);
        } catch (IOException ex) {
            Log.v("e.printStackTrace()", "PrinterData");
        }
        return 1;
    }

    public int SendPrint(int[] pData, int pLength, int Font) throws IOException {
        int[] pBuff = new int[1000];
        @SuppressWarnings("unused")
        int n, lrc = 0, space = 0, iii = 0, iBreak = 0, dataPack = 0, pPos = 0, pInc = 0;
        int iEvLength = 0;

        if (Font == 1 || Font == 2) {
            dataPack = pLength / 125 + 1;
            Log.v("pLength", pLength + "");
            Log.v("dataPack", dataPack + "");
        } else if (Font == 3 || Font == 4)
            dataPack = pLength / 86 + 1;

        System.out.println(dataPack);
        Log.v("dataPack", dataPack + "");
        if (Font == 1 || Font == 2) {
            space = pLength % 125;
            Log.v("space", space + "");
        } else if (Font == 3 || Font == 4)
            space = pLength % 86;
/////////////////// APPENDING SPACES///////////////////
        if (Font == 1 || Font == 2) {
            for (iii = 0; ; iii++) {
                iBreak = space % 25;
                Log.v("iBreak", iBreak + "");
                if (iBreak == 0)
                    break;
                pData[pLength++] = 0x20;
                space++;
            }
        } else if (Font == 3 || Font == 4) {
            for (iii = 0; ; iii++) {
                iBreak = space % 43;
                if (iBreak == 0)
                    break;
                pData[pLength++] = 0x20;
                space++;
            }
        }
/////////////////// APPENDING SPACES///////////////////
        while (dataPack > 0) {
            pBuff[0] = 0x7e;
            pBuff[1] = 0xb2;
            if (Font == 1 || Font == 2) {
                pPos = pInc * 125;
                Log.v("pPos", pPos + "");
                if (dataPack > 1) {
                    iEvLength = 125;
                    Log.v("iEvLength_IF", iEvLength + "");
                } else {
                    iEvLength = pLength - pPos;
                    Log.v("iEvLength_ELSE", iEvLength + "");
                }
            } else if (Font == 3 || Font == 4) {
                pPos = pInc * 86;
                if (dataPack > 1)
                    iEvLength = 86;
                else
                    iEvLength = pLength - pPos;
            }
            System.out.println("iEvLength=" + iEvLength + "Pos=" + pPos);
            Log.v("APPENDING SPACES", "iEvLength=" + iEvLength + "Pos=" + pPos);
            System.arraycopy(pData, pPos, pBuff, 2, iEvLength);
            iEvLength = iEvLength + 2;
            pBuff[iEvLength++] = 0x04;


            for (n = 1; n < iEvLength; n++) {
                lrc ^= pBuff[n];
            }
            pBuff[iEvLength++] = lrc;
            try {

                for (k = 0; k < iEvLength; k++) {
                    mmOutStream.write(pBuff[k]);
                    Log.v("mmOutStream", mmOutStream.toString());
                }
                mmOutStream.flush();
                ReadPrinterresp();

            } catch (IOException ex) {
                ex.printStackTrace();
                Log.v("e.printStackTrace()", "SendPrint");
            }

            dataPack--;
            pInc++;
            lrc = 0;
        }
        return 1;
    }

    public void printme(String st) {
        byte Writebuff[] = new byte[80];
        int i = 0, j = 0, lrc = 0, k = 0;

        int m = st.length();
        String hexString = stringToHex(st);
        byte[] b = hexToBuffer(hexString);

        Writebuff[0] = 0x7e;
        Writebuff[1] = (byte) 0xb2;
        Writebuff[2] = (byte) 0xf0;
        for (i = 3, j = 0; i < m + 3; i++, j++) {
            Writebuff[i] = b[j];
        }

        for (k = j; k < 24; k++)
            Writebuff[i++] = ' ';

        Writebuff[i] = '\0';
        Writebuff[i++] = 0x04;

        for (int n = 1; n < i; n++) {
            lrc ^= Writebuff[n];
        }

        Writebuff[i++] = (byte) lrc;
        Writebuff[i] = '\0';

        try {
            for (k = 0; k < i; k++)
                mmOutStream.write(Writebuff[k]);
            mmOutStream.flush();
            ReadPrinterresp();
        } catch (IOException ex) {
            ex.printStackTrace();
        }
    }

    private byte[] hexToBuffer(String hexString) {

        return null;
    }

    public void printHeader() {
        PrinterData("    THE YELLOW STRAW   ", 1);
        PrinterData("      Sweet Home      ", 1);
        PrinterData("    6A, K.S.ROY ROAD   ", 1);
        PrinterData("     KOLKATA 700001    ", 1);
        PrinterData("          ", 1);
        PrinterData("DT : " + dateStamp + "              Time : " + timeStamp, 3);
    }

    public void printFooter() {
        PrinterData("      THANK YOU !       ", 1);
        PrinterData("www.theyellowstraw.com", 1);
        PrinterData("    Unit: ABSOLUTE HEALTH ENTERPRISE", 3);
        PrinterData("          ", 1);
        PrinterData("          ", 1);
        PrinterData("          ", 1);
        PrinterData("          ", 1);
        PrinterData("          ", 1);
    }

}
