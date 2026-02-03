package com.forcepower.acedns.activity.non_auth.help;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.pdf.PdfRenderer;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.ParcelFileDescriptor;
import android.text.Html;
import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.OnSwipeTouchListener;
import com.forcepower.acedns.util.TouchImageView;
import com.forcepower.acedns.util.Utils;

import java.io.File;

public class HelpActivity extends Activity {
    Context mContext;
    TouchImageView ImageView_community_resource_document;
    int minValuePdfPage = 0;
    int maxValuePdfPage = 0;
    int currentPdfPage = 0;
    int totalPage;
    PdfRenderer renderer;
    File pdfFile;
    int currentApiVersion = Build.VERSION.SDK_INT;
    TextView pdfPageNumberTextView, vertical;
    ImageView imgLogo;

    @SuppressLint({"SetTextI18n", "ClickableViewAccessibility"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_help);
        mContext = this;
        imgLogo = findViewById(R.id.imagelogo);
        vertical = findViewById(R.id.vertical);
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        pdfFile = new File(Utils.getAppStoragePath(mContext) + Constants.starHelpFileName);
        if (Constants.nickName.toUpperCase().matches("GOLDSTONET") || Constants.nickName.toUpperCase().matches("GOLDSTONE")) {
            pdfFile = new File(Utils.getAppStoragePath(mContext) + Constants.goldStoneHelpFileName);
        }
        ImageView_community_resource_document = findViewById(R.id.imgPdfRendered);
        if (currentApiVersion >= Build.VERSION_CODES.LOLLIPOP) {
            ImageView_community_resource_document.setOnTouchListener(new OnSwipeTouchListener(mContext) {
                @Override
                public void onSwipeLeft() {
                    if (maxValuePdfPage > 0) {
                        int currentPage = 1;
                        if (currentPdfPage == 0) {
                            ShowPdfPagesInImageView(currentPdfPage + 1);
                            currentPdfPage = currentPdfPage + 1;
                            currentPage = currentPdfPage + 1;
                            pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>"));
                        } else if (currentPdfPage < maxValuePdfPage) {
                            ShowPdfPagesInImageView(currentPdfPage + 1);
                            currentPdfPage = currentPdfPage + 1;
                            currentPage = currentPdfPage + 1;
                            pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>"));
                        }
                    }
                }

                @Override
                public void onSwipeRight() {
                    if (maxValuePdfPage > 0) {
                        int currentPage;
                        if (currentPdfPage == maxValuePdfPage) {
                            currentPage = currentPdfPage;
                            ShowPdfPagesInImageView(currentPdfPage - 1);
                            currentPdfPage = currentPdfPage - 1;
                            pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>"));
                        } else if (currentPdfPage > minValuePdfPage) {
                            currentPage = currentPdfPage;
                            ShowPdfPagesInImageView(currentPdfPage - 1);
                            currentPdfPage = currentPdfPage - 1;
                            pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>"));
                        }
                    }
                }
            });
        }
        readPdfFileAndShowOnImageView();
    }

    private void ShowPdfPagesInImageView(int i) {
        try {
            renderer = new PdfRenderer(ParcelFileDescriptor.open(pdfFile, ParcelFileDescriptor.MODE_READ_ONLY));
            PdfRenderer.Page page = renderer.openPage(i);
            Bitmap mBitmap = Bitmap.createBitmap(page.getWidth(), page.getHeight(), Bitmap.Config.ARGB_8888);
            page.render(mBitmap, null, null, PdfRenderer.Page.RENDER_MODE_FOR_DISPLAY);
            ImageView_community_resource_document.setImageBitmap(mBitmap);
            page.close();
            renderer.close();
        } catch (Exception ignored) {
        }
    }

    private void readPdfFileAndShowOnImageView() {
        try {
            if (currentApiVersion >= Build.VERSION_CODES.LOLLIPOP) {
                renderer = new PdfRenderer(ParcelFileDescriptor.open(pdfFile, ParcelFileDescriptor.MODE_READ_ONLY));
                final int pageCount = renderer.getPageCount();
                if (pageCount > 0) {
                    maxValuePdfPage = pageCount - 1;
                    totalPage = pageCount;
                    pdfPageNumberTextView = findViewById(R.id.pdfPageNumberTextView);
                    pdfPageNumberTextView.setVisibility(View.VISIBLE);
                    pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + 1 + "/<font color='#F58322'>" + totalPage + "</font>"));
                }
                PdfRenderer.Page page = renderer.openPage(0);
                Bitmap mBitmap = Bitmap.createBitmap(page.getWidth(), page.getHeight(), Bitmap.Config.ARGB_4444);
                page.render(mBitmap, null, null, PdfRenderer.Page.RENDER_MODE_FOR_DISPLAY);
                ImageView_community_resource_document.setImageBitmap(mBitmap);
                page.close();
                renderer.close();
            } else {
                Intent target = new Intent(Intent.ACTION_VIEW);
                target.setDataAndType(Uri.fromFile(pdfFile), "application/pdf");
                target.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
                Intent intent = Intent.createChooser(target, "Open File");
                try {
                    startActivity(intent);
                } catch (ActivityNotFoundException ignored) {
                }
            }
        } catch (Exception ignored) {
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }
    }

    public void finishCurrentActivity(View v) {
        finish();
    }
}

