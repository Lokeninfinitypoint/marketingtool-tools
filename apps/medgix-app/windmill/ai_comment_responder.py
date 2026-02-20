import os
import sys
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from src.windmill import MetaAPI, AIClient, AppwriteDB

def ai_comment_responder():
    """Automated AI response to comments"""
    meta = MetaAPI(access_token='YOUR_META_TOKEN')
    ai = AIClient()
    db = AppwriteDB()
    
    comments = meta.get_new_comments()
    for comment in comments:
        sentiment = ai.analyze_sentiment(comment['text'])
        response = ai.generate_response(
            text=comment['text'],
            context='marketing',
            sentiment=sentiment
        )
        meta.post_reply(comment['id'], response)
        db.log_action(
            user_id=comment['page_owner'],
            action='comment_replied',
            details=f"Comment ID: {comment['id']}"
        )
