<li id="status-{{ $status->id }}">
	<a href="{{ route('users.show', $user->id) }}">
		<img src="{{ $user->gravatar() }}" alt="{{ $user->name }}" class="gravatar">
	</a>
</li>
<span class='user'>
	<a href="{{ route('users.show', $user->id) }}">{{ $user->name }}</a>
</span>
<span class="timestamp">
	{{ $status->created_at->diffForHumans() }} <!-- diffForHumans()这里是进行时间的友好化 -->
</span>
<span class="content">{{ $status->content }}</span>