-- Create travel_roadbooks table
CREATE TABLE IF NOT EXISTS public.travel_roadbooks (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    brand_id UUID NOT NULL REFERENCES public.brands(id) ON DELETE CASCADE,
    client_name TEXT NOT NULL,
    title TEXT NOT NULL,
    subtitle TEXT,
    intro_text TEXT,
    hero_image_url TEXT,
    hero_images TEXT[],
    hero_video_url TEXT,
    destinations JSONB DEFAULT '[]'::jsonb,
    items JSONB DEFAULT '[]'::jsonb,
    extra_costs JSONB DEFAULT '[]'::jsonb,
    total_price DECIMAL(10,2) DEFAULT 0,
    price_per_person DECIMAL(10,2) DEFAULT 0,
    number_of_travelers INTEGER DEFAULT 2,
    currency TEXT DEFAULT 'EUR',
    price_display TEXT DEFAULT 'both',
    status TEXT DEFAULT 'draft',
    valid_until DATE,
    internal_notes TEXT,
    travel_compositor_id TEXT,
    client_email TEXT,
    client_phone TEXT,
    agent_id UUID REFERENCES public.users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Enable RLS
ALTER TABLE public.travel_roadbooks ENABLE ROW LEVEL SECURITY;

-- RLS Policies
CREATE POLICY "Users can view roadbooks from their brand"
    ON public.travel_roadbooks
    FOR SELECT
    USING (
        brand_id IN (
            SELECT brand_id FROM public.users WHERE id = auth.uid()
        )
    );

CREATE POLICY "Users can insert roadbooks for their brand"
    ON public.travel_roadbooks
    FOR INSERT
    WITH CHECK (
        brand_id IN (
            SELECT brand_id FROM public.users WHERE id = auth.uid()
        )
    );

CREATE POLICY "Users can update roadbooks from their brand"
    ON public.travel_roadbooks
    FOR UPDATE
    USING (
        brand_id IN (
            SELECT brand_id FROM public.users WHERE id = auth.uid()
        )
    );

CREATE POLICY "Users can delete roadbooks from their brand"
    ON public.travel_roadbooks
    FOR DELETE
    USING (
        brand_id IN (
            SELECT brand_id FROM public.users WHERE id = auth.uid()
        )
    );

-- Create index for faster queries
CREATE INDEX idx_travel_roadbooks_brand_id ON public.travel_roadbooks(brand_id);
CREATE INDEX idx_travel_roadbooks_created_at ON public.travel_roadbooks(created_at DESC);
