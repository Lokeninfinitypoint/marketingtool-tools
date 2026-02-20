from meta_api import MetaAPI
from appwrite_api import AppwriteDB

def daily_optimization():
    """Automated campaign optimization"""
    meta = MetaAPI(access_token='YOUR_META_TOKEN')
    db = AppwriteDB()
    
    campaigns = meta.get_campaigns(status='ACTIVE')
    for campaign in campaigns:
        stats = meta.get_campaign_stats(campaign['id'])
        if stats['roas'] < 2.0 and stats['spend'] > 100:
            meta.pause_campaign(campaign['id'])
            db.log_action(campaign['owner'], 'campaign_paused', stats)
