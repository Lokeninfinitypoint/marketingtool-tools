#!/usr/bin/env python3
import os
import re
import shutil

# Create directories
directories = [
    'marketingtool-pro',
    'marketingtool-pro/optimization',
    'marketingtool-pro/ai-ads',
    'marketingtool-pro/analytics-workflows',
    'marketingtool-pro/about-us',
    'marketingtool-pro/blog',
    'marketingtool-pro/careers',
    'marketingtool-pro/case-studies',
    'marketingtool-pro/pricing',
    'marketingtool-pro/academy',
    'marketingtool-pro/brand',
    'marketingtool-pro/product',
    'marketingtool-pro/whats-new',
    'marketingtool-pro/why-marketingtool',
    'marketingtool-pro/workflows',
    'marketingtool-pro/ai-ads',
    'marketingtool-pro/analytics'
]

for dir in directories:
    os.makedirs(dir, exist_ok=True)

print("Directories created successfully!")
