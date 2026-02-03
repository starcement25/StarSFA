package com.forcepower.acedns.util;

import android.content.Context;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Movie;
import android.util.AttributeSet;
import android.view.View;

import com.forcepower.acedns.R;

import java.io.InputStream;

public class DeleteGIFView extends View {

    private Movie mMovie;
    private long movieStart;

    public DeleteGIFView(Context context) {
        super(context);
        initializeView();
    }

    public DeleteGIFView(Context context, AttributeSet attrs) {
        super(context, attrs);
        initializeView();
    }

    public DeleteGIFView(Context context, AttributeSet attrs, int defStyle) {
        super(context, attrs, defStyle);
        initializeView();
    }

    private void initializeView() {
        InputStream is = getContext().getResources().openRawResource(R.drawable.trans_delete);
        mMovie = Movie.decodeStream(is);
    }

    protected void onDraw(Canvas canvas) {
        canvas.drawColor(Color.TRANSPARENT);
        super.onDraw(canvas);
        long now = android.os.SystemClock.uptimeMillis();

        if (movieStart == 0) {
            movieStart = (int) now;
        }
        if (mMovie != null) {
            int relTime = (int) (now - movieStart);
            if (relTime > mMovie.duration()) {
                relTime = mMovie.duration();
            }
            mMovie.setTime(relTime);
            mMovie.draw(canvas, getWidth() - mMovie.width(), getHeight() - mMovie.height());
            if (relTime < mMovie.duration()) {
                invalidate();
            }
        }
    }
}