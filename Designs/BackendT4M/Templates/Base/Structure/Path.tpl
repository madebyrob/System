			<div class="Path">
                {@Categories}
                {?ID}<a class="Level" href="{$Url}">{$Name}</a>{/?ID}
                {?!ID}<a class="Level Root" href="{$Url}">{$._HostName}</a>{/?!ID}
                <span class="Spacer">&gt;</span>
                {/@Categories}
                {?_ArticleStatus=3}<span class="Level">{$_ArticleName}</span>{/?_ArticleStatus=3}
                {?_ArticleStatus=2}<a class="Level" href="{$_ArticleUrl}">{$_ArticleName}</a>{/?_ArticleStatus=2}
            </div>