/**
 * Observability & Logging
 * Tracks conversation flow for debugging
 */

import { SupabaseClient } from "npm:@supabase/supabase-js@2";
import { ConversationSlots } from "./state-manager.ts";

export interface ToolCall {
  tool_name: string;
  params: Record<string, any>;
  response_summary: string;
  success: boolean;
}

export interface RAGChunk {
  doc_name: string;
  snippet_id: string;
  content: string;
  relevance_score?: number;
}

export class ObservabilityLogger {
  private startTime: number;

  constructor(
    private supabase: SupabaseClient,
    private sessionToken: string,
    private tripId: string
  ) {
    this.startTime = Date.now();
  }

  async log(params: {
    messageId: string | null;
    slotsBefore: ConversationSlots;
    slotsAfter: ConversationSlots;
    ragChunks: RAGChunk[];
    toolsCalled: ToolCall[];
    modelTemperature: number;
    tokensUsed?: number;
  }): Promise<void> {
    const responseTimeMs = Date.now() - this.startTime;

    try {
      await this.supabase.from('conversation_logs').insert({
        session_token: this.sessionToken,
        trip_id: this.tripId,
        message_id: params.messageId,
        slots_before: params.slotsBefore as any,
        slots_after: params.slotsAfter as any,
        rag_chunks_used: params.ragChunks as any,
        tools_called: params.toolsCalled as any,
        model_temperature: params.modelTemperature,
        tokens_used: params.tokensUsed || null,
        response_time_ms: responseTimeMs,
        created_at: new Date().toISOString()
      });

      console.log(`📊 Logged conversation: ${responseTimeMs}ms, ${params.toolsCalled.length} tools, ${params.ragChunks.length} RAG chunks`);
    } catch (error) {
      console.error('Failed to log conversation:', error);
    }
  }
}
