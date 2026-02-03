package com.forcepower.acedns.activity;

import android.annotation.TargetApi;
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
import androidx.core.content.FileProvider;
import android.text.Html;
import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;

import com.forcepower.acedns.BuildConfig;
import com.forcepower.acedns.R;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.OnSwipeTouchListener;
import com.forcepower.acedns.util.TouchImageView;
import com.forcepower.acedns.util.Utils;

import java.io.File;

import static com.forcepower.acedns.constants.Constants.SchemeBranchName;
import static com.forcepower.acedns.constants.Constants.catalogueorSchemeVal;

public class SchemesPdfActivity extends Activity {
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
    AceDnsDatabase mAceDnsDatabase;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_catalogue_landing);
        mContext = this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        vertical = (TextView) findViewById(R.id.vertical);
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));pdfFile = new File(Utils.getAppStoragePath(mContext) + catalogueorSchemeVal);
        if (Constants.menuDetailsObj.getbranchwise_scheme_PDF().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("branchwise_scheme_PDF"))
        {
            vertical.setText(Html.fromHtml("Branch : " + "<font color='#F58322'>" + SchemeBranchName + "</font>"));
        }

//        ImageView_community_resource_document = (TouchImageView) findViewById(R.id.img);
        ImageView_community_resource_document = (TouchImageView) findViewById(R.id.imgPdfRendered);
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
//                        pdfPageNumberTextView.setText(currentPage + "/" + totalPage);
                            pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>"));
                        } else if (currentPdfPage < maxValuePdfPage) {
                            ShowPdfPagesInImageView(currentPdfPage + 1);
                            currentPdfPage = currentPdfPage + 1;
                            currentPage = currentPdfPage + 1;
//                        pdfPageNumberTextView.setText(currentPage + "/" + totalPage);
                            pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>"));
                        }
                    }
                }

                @Override
                public void onSwipeRight() {
                    if (maxValuePdfPage > 0) {
                        int currentPage = totalPage;
                        if (currentPdfPage == maxValuePdfPage) {
                            currentPage = currentPdfPage;
                            ShowPdfPagesInImageView(currentPdfPage - 1);
                            currentPdfPage = currentPdfPage - 1;
                            pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>"));
                        } else if (currentPdfPage > minValuePdfPage) {
                            currentPage = currentPdfPage;
                            ShowPdfPagesInImageView(currentPdfPage - 1);
                            currentPdfPage = currentPdfPage - 1;
//                        pdfPageNumberTextView.setText(currentPage + "/" + totalPage);
                            pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>"));
                        }
                    }
                    //Toast.makeText(CommunityResourceFileOpen.this, "swipped right", Toast.LENGTH_SHORT).show();

                }
            });
        }
        readPdfFileAndShowOnImageView();
    }

    @TargetApi(Build.VERSION_CODES.LOLLIPOP)
    private void ShowPdfPagesInImageView(int i) {
        try {
            renderer = new PdfRenderer(ParcelFileDescriptor.open(pdfFile, ParcelFileDescriptor.MODE_READ_ONLY));
            PdfRenderer.Page page = renderer.openPage(i);
            Bitmap mBitmap = Bitmap.createBitmap(page.getWidth(), page.getHeight(), Bitmap.Config.ARGB_8888);
            // say we render for showing on the screen
            page.render(mBitmap, null, null, PdfRenderer.Page.RENDER_MODE_FOR_DISPLAY);

            // do stuff with the bitmap
            ImageView_community_resource_document.setImageBitmap(mBitmap);
            // close the page
            page.close();

            // close the renderer
            renderer.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @TargetApi(Build.VERSION_CODES.LOLLIPOP)
    private void readPdfFileAndShowOnImageView() {
        try {

            if (currentApiVersion >= Build.VERSION_CODES.LOLLIPOP) {
                renderer = new PdfRenderer(ParcelFileDescriptor.open(pdfFile, ParcelFileDescriptor.MODE_READ_ONLY));
                // let us just render all pages
                final int pageCount = renderer.getPageCount();
                if (pageCount > 0) {
                    maxValuePdfPage = pageCount - 1;
                    totalPage = pageCount;
                    pdfPageNumberTextView = (TextView) findViewById(R.id.pdfPageNumberTextView);
                    pdfPageNumberTextView.setVisibility(View.VISIBLE);
//                    pdfPageNumberTextView.setText(1+"/"+pageCount);
                    pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + 1 + "/<font color='#F58322'>" + totalPage + "</font>"));
                }

                PdfRenderer.Page page = renderer.openPage(0);
                Bitmap mBitmap = Bitmap.createBitmap(page.getWidth(), page.getHeight(), Bitmap.Config.ARGB_4444);
                // say we render for showing on the screen
                page.render(mBitmap, null, null, PdfRenderer.Page.RENDER_MODE_FOR_DISPLAY);

                // do stuff with the bitmap
                ImageView_community_resource_document.setImageBitmap(mBitmap);
                // close the page
                page.close();

                // close the renderer
                renderer.close();
            } else {

                Intent target = new Intent(Intent.ACTION_VIEW);
//                target.setDataAndType(Uri.fromFile(pdfFile),"application/pdf");


                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                    target.setDataAndType(FileProvider.getUriForFile(mContext,
                            BuildConfig.APPLICATION_ID + ".provider",
                            pdfFile), "application/pdf");

                } else {
                    target.setDataAndType(Uri.fromFile(pdfFile), "application/pdf");

                }

                target.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);

                Intent intent = Intent.createChooser(target, "Open File");
                try {
                    startActivity(intent);
                } catch (ActivityNotFoundException e) {
                    // Instruct the user to install a PDF reader here, or something
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
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

