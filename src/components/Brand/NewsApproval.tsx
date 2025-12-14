import React, { useState, useEffect } from 'react';
import { supabase } from '../../lib/supabase';
import { useAuth } from '../../contexts/AuthContext';
import { WordPressNewsManager } from './News/WordPressNewsManager';
import { ExternalBuilderNews } from './News/ExternalBuilderNews';
import { InternalNewsManager } from './News/InternalNewsManager';

export function NewsApproval() {
  const { effectiveBrandId } = useAuth();
  const [websiteType, setWebsiteType] = useState<string>('internal');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (effectiveBrandId) {
      loadWebsiteInfo();
    } else {
      setLoading(false);
    }
  }, [effectiveBrandId]);

  const loadWebsiteInfo = async () => {
    if (!effectiveBrandId) return;

    try {
      const { data: brandData, error: brandError } = await supabase
        .from('brands')
        .select('website_type')
        .eq('id', effectiveBrandId)
        .maybeSingle();

      if (!brandError && brandData) {
        setWebsiteType(brandData.website_type || 'internal');
      }
    } catch (error) {
      console.error('Error loading website info:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  if (websiteType === 'wordpress') {
    return <WordPressNewsManager />;
  }

  if (websiteType === 'external_builder' || websiteType === 'quickstart') {
    return <ExternalBuilderNews />;
  }

  return <InternalNewsManager />;
}
