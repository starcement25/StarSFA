package com.forcepower.acedns.util;

import android.content.Context;
import android.graphics.Canvas;
import android.graphics.Movie;
import android.graphics.Paint;
import android.util.AttributeSet;
import android.view.View;

import java.io.IOException;

public class MenuAnimView extends View {

    private Movie movie;
    private long moviestart;

    public MenuAnimView(Context context) throws IOException {
        super(context);
        movie = Movie.decodeStream(getResources().getAssets().open("frog.gif"));
    }

    public MenuAnimView(Context context, AttributeSet attrs) throws IOException {
        super(context, attrs);
        movie = Movie.decodeStream(getResources().getAssets().open("frog.gif"));
    }

    public MenuAnimView(Context context, AttributeSet attrs, int defStyle) throws IOException {
        super(context, attrs, defStyle);
        movie = Movie.decodeStream(getResources().getAssets().open("frog.gif"));
    }

    @Override
    protected void onDraw(Canvas canvas) {
        super.onDraw(canvas);
        long now = android.os.SystemClock.uptimeMillis();
        Paint p = new Paint();
        p.setAntiAlias(true);
        if (moviestart == 0)
            moviestart = now;
        int relTime;
        relTime = (int) ((now - moviestart) % movie.duration());
        movie.setTime(relTime);
        movie.draw(canvas, 0, 0);
        this.invalidate();
    }


}